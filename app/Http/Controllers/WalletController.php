<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $walletBalance = auth()->user()->wallet;
        $transactions = WalletTransaction::where('user_id', auth()->id())
                            ->latest()
                            ->paginate(10);

        return view('wallet.index', compact('walletBalance', 'transactions'));
    }

    public function fund(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();

        // Add funds
        $user->wallet += $request->amount;
        $user->save();

        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'fund',
            'amount' => $request->amount,
            'description' => 'Manual funding',
        ]);

        return back()->with('success', 'Wallet funded successfully!');
    }
}
