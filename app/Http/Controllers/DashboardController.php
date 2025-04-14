<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        
        $month = Carbon::now()->format('Y-m');

        $transactions = Transaction::where('created_at', 'LIKE', "$month%")->with('transactionDetail.product')->get();

        $transactionsData = $transactions->groupBy(function($transaction) {
            return Carbon::parse($transaction->created_at)->format('Y-m-d');
        })->map(function($transactions, $date) {
            return [
                'date' => Carbon::parse($date)->translatedFormat('d F Y'),
                'total_transactions' => $transactions->count()
            ];
        })->values();

        $productSales = $transactions->flatMap->transactionDetail->groupBy('product_id')->map(function ($details, $productId) {
            return [
                'product_name' => $details->first()->product->name,
                'total_sold' => $details->sum('quantity')
            ];
        })->values();

        $today = Carbon::today()->format('Y-m-d');

        $totalTransactionsToday = Transaction::where('created_at', 'LIKE', "$today%")->count();
        $lastUpdated = Carbon::now()->translatedFormat('d F Y H:i');

        return view('pages.dashboard', compact('transactionsData', 'today', 'productSales', 'totalTransactionsToday', 'lastUpdated'));
    }
}
