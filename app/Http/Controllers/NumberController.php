<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Activation;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Str;

class NumberController extends Controller
{
    protected $daisyApiKey;
    protected $tellabotUser;
    protected $tellabotApiKey;
    protected $beeSmsToken;




    public function __construct()
    {
        $this->daisyApiKey = Setting::where('key', 'daisy_api_key')->value('value') ?? env('DAISYSMS_API_KEY');
        $this->tellabotUser = Setting::where('key', 'tellabot_user')->value('value') ?? env('TELLABOT_USER');
        $this->tellabotApiKey = Setting::where('key', 'tellabot_api_key')->value('value') ?? env('TELLABOT_API_KEY');
        $this->beeSmsToken = Setting::where('key', 'bee_sms_token')->value('value') ?? env('BEE_SMS_TOKEN');
    }

    public function index()
    {
        $usaServices = [
            'whatsapp' => ['name' => 'WhatsApp', 'cost' => 0.5],
            'telegram' => ['name' => 'Telegram', 'cost' => 0.4],
            'gmail' => ['name' => 'Gmail', 'cost' => 0.6],
        ];

        $activations = Activation::where('user_email', auth()->user()->email)->latest()->get();
        $services = collect($usaServices)->mapWithKeys(fn($service, $code) => [$code => ['name' => $service['name']]])->toArray();

        return view('number.services', compact('usaServices', 'activations', 'services'));
    }

    public function services()
    {
        $conversionRate = Setting::where('key', 'number_conversion_rate')->value('value') ?? 1620;
        $additionalPrice = Setting::where('key', 'number_additional_price')->value('value') ?? 0;

        // DaisySMS services
        $daisyServices = [];
        $response = Http::get('https://daisysms.com/stubs/handler_api.php', [
            'api_key' => $this->daisyApiKey,
            'action' => 'getPrices'
        ]);

        if ($response->ok()) {
            $services = $response->json();
            $usaServicesRaw = $services[187] ?? [];
            foreach ($usaServicesRaw as $serviceCode => $serviceDetails) {
                $usdPrice = $serviceDetails['cost'] ?? 0;
                $serviceName = $serviceDetails['name'] ?? strtoupper($serviceCode);
                $finalNairaPrice = round(($usdPrice * $conversionRate) + $additionalPrice, 2);
                $daisyServices[$serviceCode] = [
                    'name' => $serviceName,
                    'usd' => $usdPrice,
                    'naira' => $finalNairaPrice,
                ];
            }
        }

        // Tellabot services
        // Fetch pricing configs
        $tellabotConversion = Setting::where('key', 'tellabot_conversion_rate')->value('value') ?? 1620;
        $whatsappFixed = Setting::where('key', 'tellabot_whatsapp_price')->value('value') ?? 150;
        $telegramFixed = Setting::where('key', 'tellabot_telegram_price')->value('value') ?? 100;
        $otherGain = Setting::where('key', 'tellabot_other_gain')->value('value') ?? 0;
        
        $tellabotServices = [];
        $tellabotResponse = Http::get("https://www.tellabot.com/sims/api_command.php", [
            'cmd' => 'list_services',
            'user' => $this->tellabotUser,
            'api_key' => $this->tellabotApiKey
        ]);
        
        if ($tellabotResponse->ok()) {
            $decoded = json_decode($tellabotResponse->body(), true);
        
            if (isset($decoded['status']) && $decoded['status'] === 'ok' && is_array($decoded['message'])) {
                foreach ($decoded['message'] as $item) {
                    if (!isset($item['name'], $item['price'])) continue;
        
                    $usd = floatval($item['price']);
                    $code = Str::slug($item['name']);
        
                    if ($code === 'whatsapp') {
                        $finalPrice = $whatsappFixed;
                        $adminGain = $whatsappFixed;
                    } elseif ($code === 'telegram') {
                        $finalPrice = $telegramFixed;
                        $adminGain = $telegramFixed;
                    } else {
                        $finalPrice = round(($usd * $tellabotConversion) + $otherGain, 2);
                        $adminGain = $otherGain; // Fixed admin gain for other services
                    }
        
                    $tellabotServices[] = [
                        'code' => $code,
                        'name' => $item['name'],
                        'usd' => $usd,
                        'naira' => $finalPrice,
                        'markup' => $adminGain,
                    ];
                }
            }
        }
        
        
        $activations = Activation::where('user_email', auth()->user()->email)->latest()->get();
        return view('number.services', [
            'usaServices' => $daisyServices, // fix: map $daisyServices into 'usaServices'
            'tellabotServices' => $tellabotServices,
            'activations' => $activations
        ]);
        
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'service' => 'required|string',
            'cost' => 'required|numeric',
        ]);
    
        $user = auth()->user();
    
        if ($user->wallet < $request->cost) {
            return back()->withErrors(['error' => 'Insufficient balance. Kindly fund your wallet.']);
        }
    
        // 🟡 Call Daisy API
        $response = Http::get('https://daisysms.com/stubs/handler_api.php', [
            'api_key' => $this->daisyApiKey,
            'action' => 'getNumber',
            'service' => $request->service
        ]);
    
        $data = explode(':', $response->body());
    
        // ✅ Validate response
        if (count($data) < 3 || $data[0] !== 'ACCESS_NUMBER') {
            return back()->withErrors(['error' => 'Daisy error: ' . $response->body()]);
        }
    
        $activationId = $data[1];
        $number = $data[2];
    
        // ✅ Prevent duplicate entry
        $existing = Activation::where('activation_id', $activationId)->first();
    
        if ($existing) {
            return back()->with('error', 'This number has already been processed. Please try another.');
        }
    
        // ✅ Deduct wallet
        $user->wallet -= $request->cost;
        $user->save();
    
        // ✅ Create activation
        Activation::create([
            'user_email' => $user->email,
            'activation_id' => $activationId,
            'phone_number' => $number,
            'service' => $request->service,
            'service_name' => $request->input('service_name_daisy'),
            'cost' => $request->cost,
            'status' => 'Waiting',
            'source' => 'daisy'
        ]);
    
        return redirect()->route('number.services')->with('message', 'Number purchased successfully!');
    }
    

    
    public function server2Purchase(Request $request)
{
    $request->validate([
        'service' => 'required|string',
        'service_name' => 'required|string',
        'markup' => 'required|numeric|min:10',
        'cost' => 'required|numeric'
    ]);

    $user = auth()->user();

    if ($user->wallet < $request->cost) {
        return back()->withErrors(['error' => 'Insufficient wallet balance.']);
    }

    $url = "https://www.tellabot.com/sims/api_command.php?cmd=request"
         . "&user={$this->tellabotUser}"
         . "&api_key={$this->tellabotApiKey}"
         . "&service=" . urlencode($request->service_name)
         . "&markup={$request->markup}";

    $response = Http::get($url);
    $data = $response->json();

    if (!isset($data['message'][0]['id'])) {
        return back()->withErrors(['error' => 'Invalid Tellabot response: ' . json_encode($data)]);
    }

    $order = $data['message'][0];
    $orderId = $order['id'];
    $mdn = $order['mdn'] ?? null;

    if (Activation::where('activation_id', $orderId)->exists()) {
        return back()->withErrors(['error' => 'This order already exists in Tellabot. Check your activation list.']);
    }

    // Deduct wallet
    $user->wallet -= $request->cost;
    $user->save();

    // Save order
    Activation::create([
        'user_email' => $user->email,
        'activation_id' => $orderId,
        'phone_number' => $mdn,
        'service' => $request->service,
        'service_name' => $request->service_name,
        'cost' => $request->cost,
        'status' => 'Waiting',
        'source' => 'tellabot'
    ]);

    return redirect()->route('number.services')->with('message', 'Order placed successfully!');
}


public function tellabotCheckCode($id)
{
    $activation = \App\Models\Activation::findOrFail($id);

    // Step 1: Fetch SMS code
    $smsUrl = "https://www.tellabot.com/sims/api_command.php?cmd=read_sms"
            . "&user={$this->tellabotUser}"
            . "&api_key={$this->tellabotApiKey}"
            . "&id={$activation->activation_id}";

    $smsResponse = Http::get($smsUrl);
    $smsData = $smsResponse->json();

    $updates = [];

    if (
        isset($smsData['status']) && $smsData['status'] === 'ok' &&
        isset($smsData['message'][0])
    ) {
        $msg = $smsData['message'][0];

        if (!$activation->phone_number && !empty($msg['mdn'])) {
            $updates['phone_number'] = $msg['mdn'];
        }

        if (!empty($msg['pin']) && !$activation->code) {
            $updates['code'] = $msg['pin'];
            $updates['status'] = 'Received';
        }
    }

    // Step 2: Fetch latest MDN status from request_status
    $statusUrl = "https://www.tellabot.com/sims/api_command.php?cmd=request_status"
               . "&user={$this->tellabotUser}"
               . "&api_key={$this->tellabotApiKey}"
               . "&id={$activation->activation_id}";

    $statusResponse = Http::get($statusUrl);
    $statusData = $statusResponse->json();

    if (isset($statusData['message']) && is_array($statusData['message'])) {
        $statusOrder = $statusData['message'];

        $apiMdn = trim($statusOrder['mdn'] ?? '');
        $apiStatus = trim($statusOrder['status'] ?? '');

        if (!empty($apiStatus)) {
            $updates['status'] = $apiStatus;
        }

        if (!empty($apiMdn) && !$activation->phone_number) {
            $updates['phone_number'] = $apiMdn;
        }
    }

    // Step 3: Save to DB
    if (!empty($updates)) {
        $activation->update($updates);
    }

    return response()->json([
        'status' => !empty($updates) ? 'updated' : 'waiting',
        'data' => $updates
    ]);
}

    public function cancel($id)
    {
        $activation = Activation::findOrFail($id);

        $response = Http::get('https://daisysms.com/stubs/handler_api.php', [
            'api_key' => $this->daisyApiKey,
            'action' => 'getStatus',
            'id' => $activation->activation_id
        ]);

        $data = explode(':', $response->body());

        if ($data[0] == 'STATUS_OK') {
            $activation->update([
                'code' => $data[1],
                'status' => 'Received'
            ]);
            return back()->with('message', 'Order already completed. Code has arrived.');
        }

        Http::get('https://daisysms.com/stubs/handler_api.php', [
            'api_key' => $this->daisyApiKey,
            'action' => 'setStatus',
            'id' => $activation->activation_id,
            'status' => 8
        ]);

        $user = User::where('email', $activation->user_email)->first();
        if ($user) {
            $user->wallet += $activation->cost;
            $user->save();
        }

        $activation->delete();
        return redirect()->route('number.services')->with('message', '₦' . number_format($activation->cost, 2) . ' has been refunded.');

    }

    public function tellabotCancel($id)
    {
        $activation = Activation::findOrFail($id);
        $url = "https://www.tellabot.com/sims/api_command.php?cmd=reject&user={$this->tellabotUser}&api_key={$this->tellabotApiKey}&id={$activation->activation_id}";
        Http::get($url);

        $user = User::where('email', $activation->user_email)->first();
        if ($user) {
            $user->wallet += $activation->cost;
            $user->save();
        }

        $activation->delete();
        return redirect()->route('number.services')->with('message', '₦' . number_format($activation->cost, 2) . ' has been refunded.');
    }

    public function activations()
    {
        $activations = Activation::latest()->get();
        $services = get_services();
        return view('number.activations', compact('activations', 'services'));
    }

   
    public function checkCode($id)
    {
        $activation = Activation::findOrFail($id);
    
        if ($activation->code) {
            return response()->json([
                'status' => 'Received',
                'code' => $activation->code
            ]);
        }
    
        $response = Http::get('https://daisysms.com/stubs/handler_api.php', [
            'api_key' => $this->daisyApiKey,
            'action' => 'getStatus',
            'id' => $activation->activation_id
        ]);
    
        $data = explode(':', $response->body());
    
        if ($data[0] === 'STATUS_OK') {
            $activation->update([
                'code' => $data[1],
                'status' => 'Received'
            ]);
    
            return response()->json([
                'status' => 'Received',
                'code' => $data[1]
            ]);
        }
    
        return response()->json(['status' => 'Waiting']);
        
    }
    public function syncTellabotMdnsWithService()
    {
        $pending = Activation::where('source', 'tellabot')
            ->whereNull('phone_number')
            ->get();
    
        foreach ($pending as $activation) {
            $url = "https://www.tellabot.com/sims/api_command.php?cmd=request"
                 . "&user={$this->tellabotUser}"
                 . "&api_key={$this->tellabotApiKey}"
                 . "&service=" . urlencode($activation->service_name)
                 . "&markup=" . $activation->cost;
    
            $response = Http::get($url);
            if ($response->failed()) continue;
    
            $data = $response->json();
    
            if (
                isset($data['status']) && $data['status'] === 'ok' &&
                isset($data['message']) &&
                is_array($data['message']) &&
                isset($data['message'][0])
            ) {
                $msg = $data['message'][0];
                $orderId = $msg['id'] ?? null;
                $mdn = $msg['mdn'] ?? null;
    
                if ($orderId && $mdn && $orderId == $activation->activation_id) {
                    $activation->update([
                        'phone_number' => $mdn
                    ]);
                }
            }
        }
    
        return response()->json(['status' => 'success', 'message' => 'MDNs updated from Tellabot polling.']);
    }
    
    
    public function webhook(Request $request)
    {
        $activation = Activation::where('activation_id', $request->activationId)->first();
        if ($activation) {
            $activation->update([
                'code' => $request->code,
                'status' => 'Received'
            ]);
        }
        return response()->json(['status' => 'success']);
    }







    
}  