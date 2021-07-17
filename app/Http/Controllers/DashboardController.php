<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Savings;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        for ($i = 0; $i < 12; $i++) {
            $totalWithdraw[$i] = Withdraw::whereMonth('created_at', $i)->sum(
                'nominal'
            );
            $totalDeposit[$i] = Deposit::whereMonth('created_at', $i)->sum(
                'nominal'
            );
        }
        return response()->json([
            'success' => 200,
            'data' => [
                'total_tabungan' => Savings::sum('saldo'),
                'total_withdraw' => Withdraw::sum('nominal'),
                'total_deposit' => Deposit::sum('nominal'),
                'perbulan_withdraw' => $totalWithdraw,
                'perbulan_deposit' => $totalDeposit,
            ],
        ]);
    }
}
