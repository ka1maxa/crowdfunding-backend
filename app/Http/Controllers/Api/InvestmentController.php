<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    /**
     * ინვესტიციის განხორციელება (სიმულაცია)
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'investor' && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'ინვესტიციის განხორციელება მხოლოდ ინვესტორს შეუძლია.'], 403);
        }

        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount'  => 'required|numeric|min:10',
        ]);

        $loan = Loan::findOrFail($validated['loan_id']);

        if ($loan->status !== 'active') {
            return response()->json(['message' => 'ამ სესხზე ინვესტიციების მიღება დასრულებულია ან შეჩერებულია.'], 400);
        }

        $remainingAmount = $loan->amount_requested - $loan->amount_raised;

        if ($validated['amount'] > $remainingAmount) {
            return response()->json([
                'message' => "ინვესტიციის თანხა აჭარბებს საჭირო ოდენობას. მაქსიმალური შესაძლო ინვესტიცია: {$remainingAmount} ₾"
            ], 400);
        }

        DB::transaction(function () use ($request, $loan, $validated) {
            Investment::create([
                'investor_id' => $request->user()->id,
                'loan_id'     => $loan->id,
                'amount'      => $validated['amount'],
            ]);

            $loan->amount_raised += $validated['amount'];

            if ($loan->amount_raised >= $loan->amount_requested) {
                $loan->status = 'fully_funded';
            }

            $loan->save();
        });

        return response()->json([
            'message' => 'ინვესტიცია წარმატებით განხორციელდა (სიმულაცია)!',
            'loan'    => $loan->fresh(),
        ], 201);
    }

    /**
     * ჩემი ინვესტიციები
     */
    public function myInvestments(Request $request)
    {
        $investments = Investment::with('loan')
            ->where('investor_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($investments);
    }
}