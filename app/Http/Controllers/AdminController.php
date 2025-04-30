<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Order;
use App\Models\User;
use App\Models\Setting;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ===== Admin Auth =====

    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard')->with('message', 'Admin login successful');
        }

        return back()->withErrors(['email' => 'Invalid credentials provided.']);
    }
    public function listUsers(Request $request)
    {
        $query = \App\Models\User::query();
    
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
    
        $users = $query->latest()->paginate(10)->withQueryString();
    
        return view('admin.users.index', compact('users'));
    }
    

    // ===== Dashboard =====
    public function deleteApiProduct($id)
    {
        $product = Product::findOrFail($id);
    
        // Prevent manual products from being deleted via API route
        if ($product->productStocks()->exists()) {
            return back()->with('error', 'Cannot delete a manual (uploaded) product from this route.');
        }
    
        $product->delete();
    
        return back()->with('message', 'API product deleted successfully.');
    }
    
    public function index()
    {
        $totalOrders = Order::count();
        $totalTransactions = WalletTransaction::count();
        $totalRevenue = Order::sum('total_price');
        $orders = Order::with('product')->latest()->paginate(5);

        return view('admin.dashboard', compact('totalOrders', 'totalTransactions', 'totalRevenue', 'orders'));
    }

    // ===== Category Management =====
    public function manageCategories()
    {
        $categories = Category::latest()->paginate(10); // ✅ this returns a proper collection
    
        return view('admin.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            // No need to validate is_admin_created if you are setting it manually
        ]);
    
        Category::create([
            'name' => $request->name,
            'is_admin_created' => true, // ✅ Set manually here, not in validation
        ]);
    
        return redirect()->route('admin.categories')->with('message', 'Category created successfully.');
    }
    

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update(['name' => $request->name]);

        return redirect()->route('admin.categories')->with('message', 'Category updated successfully.');
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return back()->with('message', 'Category deleted successfully.');
    }

    // ===== Product Management =====

    public function manageProducts()
    {
        $categories = Category::all();
        $products = Product::with('category')->latest()->paginate(20);

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function showUploadForm()
    {
        $categories = Category::all();
        return view('admin.products.upload', compact('categories'));
    }

    public function uploadProducts(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'file' => 'required|mimes:txt',
        ]);

        $lines = file($request->file('file')->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $product = Product::create([
            'name' => $request->product_name,
            'description' => $request->default_description ?? 'Bulk uploaded product',
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => count($lines),
        ]);

        foreach ($lines as $line) {
            ProductStock::create([
                'product_id' => $product->id,
                'credential' => trim($line),
            ]);
        }

        return back()->with('message', 'Products uploaded successfully!');
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);

        if ($product->productStocks()->exists()) {
            $product->productStocks()->delete();
            $product->delete();

            return back()->with('message', 'Manual product deleted successfully.');
        }

        return back()->with('error', 'This product cannot be deleted (not manual upload).');
    }

    public function addStockForm($productId)
    {
        $product = Product::findOrFail($productId);
        return view('admin.products.add-stock', compact('product'));
    }

    public function storeStock(Request $request, $productId)
    {
        $request->validate([
            'file' => 'required|mimes:txt',
        ]);

        $product = Product::findOrFail($productId);
        $lines = file($request->file('file')->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            ProductStock::create([
                'product_id' => $product->id,
                'credential' => trim($line),
            ]);
        }

        $product->increment('stock', count($lines));

        return redirect()->route('admin.manageProducts')->with('message', 'Stock added successfully!');
    }

    // ===== Orders =====

    public function manageOrders()
    {
        $orders = Order::with('product', 'user')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function viewOrder($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.users.view-order', compact('order'));
    }

    // ===== Settings =====

    public function settings()
    {
        return view('admin.settings.index', [
            'additionalPrice' => Setting::where('key', 'additional_price')->value('value') ?? 0,
            'conversionRate' => Setting::where('key', 'conversion_rate')->value('value') ?? 1620,
            'numberConversionRate' => Setting::where('key', 'number_conversion_rate')->value('value') ?? 1620,
            'numberAdditionalPrice' => Setting::where('key', 'number_additional_price')->value('value') ?? 0,
            'daisyApiKey' => Setting::where('key', 'daisy_api_key')->value('value') ?? '',
            'tellabot_conversion_rate' => Setting::where('key', 'tellabot_conversion_rate')->value('value') ?? 1620,
'tellabot_markup_whatsapp' => Setting::where('key', 'tellabot_markup_whatsapp')->value('value') ?? 0,
'tellabot_markup_telegram' => Setting::where('key', 'tellabot_markup_telegram')->value('value') ?? 0,
'tellabot_markup_default' => Setting::where('key', 'tellabot_markup_default')->value('value') ?? 0,
'tellabot_whatsapp_price' => Setting::where('key', 'tellabot_whatsapp_price')->value('value') ?? 150,
'tellabot_telegram_price' => Setting::where('key', 'tellabot_telegram_price')->value('value') ?? 100,
'tellabot_other_gain' => Setting::where('key', 'tellabot_other_gain')->value('value') ?? 0,
'tellabot_user' => Setting::where('key', 'tellabot_user')->value('value'),
'tellabot_api_key' => Setting::where('key', 'tellabot_api_key')->value('value'),




        ]);
    }
    
    public function updateSettings(Request $request)
    {
        $request->validate([
            'additional_price' => 'required|numeric|min:0',
            'conversion_rate' => 'required|numeric|min:1',
            'number_conversion_rate' => 'required|numeric|min:1',
            'number_additional_price' => 'required|numeric|min:0',
            'daisy_api_key' => 'required|string',
            'tellabot_conversion_rate' => 'required|numeric|min:1',
            'tellabot_markup_whatsapp' => 'required|numeric|min:0',
            'tellabot_markup_telegram' => 'required|numeric|min:0',
            'tellabot_markup_default' => 'required|numeric|min:0',
            'tellabot_whatsapp_price' => 'required|numeric|min:0',
            'tellabot_other_gain' => 'required|numeric|min:0',
'tellabot_telegram_price' => 'required|numeric|min:0',
'tellabot_user' => 'required|string',
'tellabot_api_key' => 'required|string',

        ]);
        
        Setting::updateOrCreate(['key' => 'additional_price'], ['value' => $request->additional_price]);
        Setting::updateOrCreate(['key' => 'conversion_rate'], ['value' => $request->conversion_rate]);
        Setting::updateOrCreate(['key' => 'number_conversion_rate'], ['value' => $request->number_conversion_rate]);
        Setting::updateOrCreate(['key' => 'number_additional_price'], ['value' => $request->number_additional_price]);
        Setting::updateOrCreate(['key' => 'daisy_api_key'], ['value' => $request->daisy_api_key]);
        Setting::updateOrCreate(['key' => 'tellabot_other_gain'], ['value' => $request->tellabot_other_gain]);

        Setting::updateOrCreate(['key' => 'tellabot_conversion_rate'], ['value' => $request->tellabot_conversion_rate]);
        Setting::updateOrCreate(['key' => 'tellabot_markup_whatsapp'], ['value' => $request->tellabot_markup_whatsapp]);
        Setting::updateOrCreate(['key' => 'tellabot_markup_telegram'], ['value' => $request->tellabot_markup_telegram]);
        Setting::updateOrCreate(['key' => 'tellabot_markup_default'], ['value' => $request->tellabot_markup_default]);
        Setting::updateOrCreate(['key' => 'tellabot_whatsapp_price'], ['value' => $request->tellabot_whatsapp_price]);
Setting::updateOrCreate(['key' => 'tellabot_telegram_price'], ['value' => $request->tellabot_telegram_price]);
Setting::updateOrCreate(['key' => 'tellabot_user'], ['value' => $request->tellabot_user]);
Setting::updateOrCreate(['key' => 'tellabot_api_key'], ['value' => $request->tellabot_api_key]);

        
    
        return back()->with('message', 'Settings updated successfully!');
    }
    
    // ===== Transactions =====

    public function manageTransactions()
    {
        $transactions = WalletTransaction::with('user')->latest()->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }

    // ===== User Management =====

   

    public function viewUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.view', compact('user'));
    }

    public function fundUser(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        $user = User::findOrFail($id);
        $user->increment('wallet', $request->amount);

        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'fund',
            'amount' => $request->amount,
            'description' => 'Wallet funded by Admin',
        ]);

        return back()->with('message', 'User wallet funded successfully!');
    }

    public function debitUser(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        $user = User::findOrFail($id);
        if ($user->wallet < $request->amount) {
            return back()->with('error', 'User has insufficient wallet balance.');
        }

        $user->decrement('wallet', $request->amount);

        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'debit',
            'amount' => $request->amount,
            'description' => 'Wallet debited by Admin',
        ]);

        return back()->with('message', 'User wallet debited successfully!');
    }

    public function banUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = true;
        $user->save();

        return back()->with('message', 'User has been banned successfully.');
    }

    public function unbanUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = false;
        $user->save();

        return back()->with('message', 'User has been unbanned successfully.');
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->password = bcrypt($request->new_password);
        $user->save();

        return back()->with('message', 'User password reset successfully!');
    }

    public function userOrders($id)
    {
        $user = User::findOrFail($id);
        $orders = $user->orders()->latest()->paginate(10);
        return view('admin.users.orders', compact('user', 'orders'));
    }

    public function userWalletTransactions($id)
    {
        $user = User::findOrFail($id);
        $transactions = $user->walletTransactions()->latest()->paginate(10);
        return view('admin.users.transactions', compact('user', 'transactions'));
    }
}
