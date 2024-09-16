<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Carbon\Carbon;
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
        // Check if the month is passed as a name (e.g., "September") or number (e.g., "9")
        if (is_numeric($month)) {
            $month = Carbon::create()->month($month)->format('F');
        } else {
            $month = ucfirst($month);
        }

        // Get the transactions for the given month
        $transactions = Transaction::whereMonth('created_at', Carbon::parse($month)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->get();

        $summary = [];

        // Group the transactions by wallet and calculate total credits and debits
        foreach ($transactions as $transaction) {
            if (!isset($summary[$transaction->from_wallet_id])) {
                $summary[$transaction->from_wallet_id] = ['wallet_id' => $transaction->from_wallet_id, 'total_credits' => 0, 'total_debits' => 0];
            }

            if (!isset($summary[$transaction->to_wallet_id])) {
                $summary[$transaction->to_wallet_id] = ['wallet_id' => $transaction->to_wallet_id, 'total_credits' => 0, 'total_debits' => 0];
            }

            $summary[$transaction->from_wallet_id]['total_debits'] += $transaction->amount;
            $summary[$transaction->to_wallet_id]['total_credits'] += $transaction->amount;
        }

        return response()->json([
            'month' => $month,
            'summary' => array_values($summary)
        ], 200);
    }

}

