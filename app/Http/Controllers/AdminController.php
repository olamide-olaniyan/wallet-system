<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Transaction;

class AdminController extends Controller
{
    /**
     * Approve transfers.
     * @param $id
     * @return JsonResponse
     */
    public function approveTransfer($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->status === 'pending') {
            $fromWallet = Wallet::findOrFail($transaction->from_wallet_id);
            $toWallet = Wallet::findOrFail($transaction->to_wallet_id);

            $fromWallet->balance -= $transaction->amount;
            $toWallet->balance += $transaction->amount;

            $fromWallet->save();
            $toWallet->save();

            $transaction->status = 'approved';
            $transaction->save();
        }

        return response()->json(['message' => 'Transfer approved']);
    }

    /**
     * Get monthly summary.
     * @param $month
     * @return JsonResponse
     */
    public function monthlySummary($month)
    {
        $transactions = Transaction::whereMonth('created_at', $month)->get();

        return response()->json(['transactions' => $transactions]);
    }

}

