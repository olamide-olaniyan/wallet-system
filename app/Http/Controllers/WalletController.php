<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Unicodeveloper\Paystack\Facades\Paystack;

class WalletController extends Controller
{

    /**
     * Create a Wallet.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'currency' => 'required',
        ]);

        $wallet = Wallet::create([
            'user_id' => auth()->id(),
            'currency' => $request->currency,
        ]);

        return response()->json(['wallet' => $wallet]);
    }


    /**
     * Initiate payment to credit the wallet.
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function credit(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:100',
            'email' => 'required|email',
        ]);

        // Prepare data for the transaction
        $paymentDetails = [
            'amount' => $request->amount * 100,
            'email' => $request->email,
            'currency' => 'NGN',
            'metadata' => [
                'wallet_id' => $wallet->id,
            ],
            'callback_url' => route('paystack.callback'),
        ];

        // Redirect to Paystack payment authorization URL
        return Paystack::getAuthorizationUrl($paymentDetails)->redirectNow();
    }

    /**
     * Handle Paystack payment verification callback.
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyCredit(Request $request)
    {
        // Verify the transaction using the Paystack reference
        $paymentDetails = Paystack::getPaymentData();

        // Check if the payment was successful
        if ($paymentDetails['status'] === true) {
            // Retrieve transaction details
            $amount = $paymentDetails['data']['amount'] / 100;
            $wallet_id = $paymentDetails['data']['metadata']['wallet_id'];

            // Find the corresponding wallet and credit it
            $wallet = Wallet::findOrFail($wallet_id);
            $wallet->balance += $amount;
            $wallet->save();

            return response()->json([
                'message' => 'Wallet credited successfully',
                'balance' => $wallet->balance
            ]);
        } else {
            return response()->json([
                'message' => 'Payment verification failed',
            ], 400);
        }
    }


    /**
     * Initiate a transfer between wallets.
     * @param Request $request
     * @return JsonResponse
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'from_wallet_id' => 'required',
            'to_wallet_id' => 'required',
            'amount' => 'required|numeric',
        ]);

        $fromWallet = Wallet::findOrFail($request->from_wallet_id);
        $toWallet = Wallet::findOrFail($request->to_wallet_id);

        if ($fromWallet->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        $transaction = Transaction::create([
            'from_wallet_id' => $fromWallet->id,
            'to_wallet_id' => $toWallet->id,
            'amount' => $request->amount,
            'status' => $request->amount > 1000000 ? 'pending' : 'completed',
        ]);

        if ($transaction->status === 'completed') {
            $fromWallet->balance -= $request->amount;
            $toWallet->balance += $request->amount;
            $fromWallet->save();
            $toWallet->save();
        }

        return response()->json(['transaction' => $transaction]);
    }

}

