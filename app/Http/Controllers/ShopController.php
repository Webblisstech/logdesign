<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Product;

use App\Models\Setting;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = 'aada198608ece1ebb755cd878415fd18'; // 🔁 your API key
        $this->baseUrl = 'https://shopviaclone22.com/api';    // 🔁 your API base URL
    }

    public function dashboard()
    {
        $categories = \App\Models\Category::with('products')->get();
        $adminGain = (float) Setting::where('key', 'additional_price')->value('value') ?? 0;
        $conversionRate = (float) Setting::where('key', 'conversion_rate')->value('value') ?? 1620;
    
        return view('dashboard', compact('categories', 'adminGain', 'conversionRate'));
    }
    


    public function product($id)
    {
        $productResponse = Http::get($this->baseUrl . '/product.php', [
            'api_key' => $this->apiKey,
            'product' => $id,
        ]);
    
        $productData = $productResponse->json();
        $product = $productData['product'][0] ?? [];
    
        $adminGain = (float) \App\Models\Setting::where('key', 'additional_price')->value('value') ?? 0;
        $conversionRate = (float) \App\Models\Setting::where('key', 'conversion_rate')->value('value') ?? 1620;
    
        return view('product', compact('product', 'adminGain', 'conversionRate'));
    }
    
    public function purchase(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
    
        $user = auth()->user();
        $quantity = (int) $request->quantity;
    
        // Step 1: Try to find product in local database
        $localProduct = \App\Models\Product::find($id);
    
        if ($localProduct && empty($localProduct->api_product_id)) {
            // 🟢 This is a MANUAL (Admin uploaded) Product
    
            // Fetch available unsold stock
            $availableStocks = \App\Models\ProductStock::where('product_id', $localProduct->id)
                ->where('sold', false)
                ->limit($quantity)
                ->get();
    
            if ($availableStocks->count() < $quantity) {
                return back()->with('error', 'Insufficient stock available.');
            }
    
            $totalAmount = $localProduct->price * $quantity;
    
            if ($user->wallet < $totalAmount) {
                return back()->with('error', 'Insufficient wallet balance.');
            }
    
            // Fetch credentials
            $orderDetails = $availableStocks->pluck('credential')->implode("\n");
    
            // Mark stock lines as sold
            foreach ($availableStocks as $stock) {
                $stock->sold = true;
                $stock->save();
            }
    
            // Deduct wallet
            $user->wallet -= $totalAmount;
            $user->save();
    
            // Decrease stock
            $localProduct->stock -= $quantity;
            $localProduct->save();
    
            // Save Order
            \App\Models\Order::create([
                'user_id' => $user->id,
                'product_id' => $localProduct->id,
                'quantity' => $quantity,
                'api_order_id' => null,
                'total_price' => $totalAmount,
                'order_details' => $orderDetails,
                'status' => 'completed',
                'product_name' => $localProduct->name,
            ]);
    
            // Log Wallet Transaction
            \App\Models\WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'amount' => $totalAmount,
                'description' => 'Purchased manual product: ' . $localProduct->name,
            ]);
    
            return redirect()->route('dashboard')->with('message', 'Product purchased successfully!');
        }
    
        // Step 2: API Product (Product from External API)
        $productResponse = Http::get('https://shopviaclone22.com/api/product.php', [
            'api_key' => $this->apiKey,
            'product' => $id,
        ]);
    
        $productData = $productResponse->json();
    
        if (!isset($productData['product'][0]['price'])) {
            return back()->with('error', 'Failed to fetch product price.');
        }
    
        $productPriceUSD = (float) $productData['product'][0]['price'];
        $productName = $productData['product'][0]['name'] ?? 'Unknown Product';
    
        $conversionRate = (float) \App\Models\Setting::where('key', 'conversion_rate')->value('value') ?? 1500;
        $adminGain = (float) \App\Models\Setting::where('key', 'additional_price')->value('value') ?? 0;
    
        $productPriceNaira = $productPriceUSD * $conversionRate;
        $productPriceFinal = $productPriceNaira + $adminGain;
    
        $totalAmount = $productPriceFinal * $quantity;
    
        if ($user->wallet < $totalAmount) {
            return back()->with('error', 'Insufficient wallet balance.');
        }
    
        // Deduct wallet immediately
        $user->wallet -= $totalAmount;
        $user->save();
    
        // Log Wallet Transaction for Purchase
        \App\Models\WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount' => $totalAmount,
            'description' => 'Purchase of API product: ' . $productName,
        ]);
    
        // Attempt API purchase
        $buyResponse = Http::asForm()->post('https://shopviaclone22.com/api/buy_product', [
            'action' => 'buyProduct',
            'id' => $id,
            'amount' => $quantity,
            'coupon' => $request->coupon ?? '',
            'api_key' => $this->apiKey,
        ]);
    
        $response = $buyResponse->json();
    
        if (isset($response['status']) && $response['status'] == 'success') {
            $apiOrderId = $response['trans_id'] ?? null;
            $orderDataArray = $response['data'] ?? [];
    
            $orderDetails = is_array($orderDataArray) && count($orderDataArray) > 0
                            ? implode("\n", $orderDataArray)
                            : '';
    
            // Save Order
            \App\Models\Order::create([
                'user_id' => $user->id,
                'product_id' => $id,
                'quantity' => $quantity,
                'api_order_id' => $apiOrderId,
                'total_price' => $totalAmount,
                'order_details' => $orderDetails,
                'status' => 'completed',
                'product_name' => $productName,
            ]);
    
            return redirect()->route('dashboard')->with('message', 'Product purchased successfully!');
        } else {
            // Refund wallet if API fails
            $user->wallet += $totalAmount;
            $user->save();
    
            // Log refund transaction
            \App\Models\WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'refund',
                'amount' => $totalAmount,
                'description' => 'Refund: API purchase failed. ' . ($response['message'] ?? 'Unknown error'),
            ]);
    
            return back()->with('error', $response['message'] ?? 'Purchase failed, amount refunded.');
        }
    }
    
    
    public function manualOrder($id)
    {
        $order = \App\Models\Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    
        return view('order', compact('order'));
    }
    


    public function userOrders()
    {
        $orders = Order::where('user_id', auth()->id())
                       ->with('product') // Eager load the product relation
                       ->latest()
                       ->paginate(10); // Pagination for better UX
    
        return view('orders.index', compact('orders')); // Pass orders to the view
    }
    
public function order($apiOrderId)
{
    $order = Order::where('api_order_id', $apiOrderId)
                  ->where('user_id', auth()->id())
                  ->first();

    return view('order', compact('order'));
}
public function syncCategories()
{
    $productsResponse = Http::get($this->baseUrl . '/products.php', [
        'api_key' => $this->apiKey,
    ]);

    $productsData = $productsResponse->json();

    if (isset($productsData['categories']) && is_array($productsData['categories'])) {
        foreach ($productsData['categories'] as $category) {
            if (isset($category['name'])) {
                // Check if category already exists
                $existingCategory = \App\Models\Category::where('name', $category['name'])->first();
                if (!$existingCategory) {
                    \App\Models\Category::create([
                        'name' => $category['name'],
                    ]);
                }
            }
        }

        return back()->with('message', 'Categories synced successfully!');
    } else {
        return back()->with('error', 'Failed to fetch categories from API.');
    }
}
public function syncProducts()
{
    $productsResponse = Http::get($this->baseUrl . '/products.php', [
        'api_key' => $this->apiKey,
    ]);

    $productsData = $productsResponse->json();

    if (isset($productsData['categories']) && is_array($productsData['categories'])) {
        foreach ($productsData['categories'] as $category) {
            // First ensure the category exists
            $localCategory = \App\Models\Category::firstOrCreate(
                ['name' => $category['name']],
                ['created_at' => now(), 'updated_at' => now()]
            );

            if (isset($category['products']) && is_array($category['products'])) {
                foreach ($category['products'] as $product) {
                    if (isset($product['id'], $product['name'])) {
                        \App\Models\Product::updateOrCreate(
                            ['api_product_id' => $product['id']], // Find by API product ID
                            [
                                'name' => $product['name'],
                                'description' => $product['description'] ?? null,
                                'category_id' => $localCategory->id,
                                'price' => isset($product['price']) ? (float) $product['price'] : null,
                                'stock' => isset($product['amount']) ? (int) $product['amount'] : 0, // 🔥 Save the amount here as stock!
                                'updated_at' => now(),
                            ]
                        );
                    }
                }
            }
        }

        return back()->with('message', 'Products synced successfully!');
    } else {
        return back()->with('error', 'Failed to fetch products from API.');
    }
}


}
