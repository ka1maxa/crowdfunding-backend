<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * ყველა აქტიური სესხის/პროექტის სია
     */
    public function index()
    {
        $loans = Loan::with('borrower:id,name,email')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($loans);
    }

    /**
     * ახალი სესხის/პროექტის შექმნა (სტარტაპერისთვის)
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'borrower' && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'სესხის განაცხადის შექმნა მხოლოდ სტარტაპერს/მსესხებელს შეუძლია.'], 403);
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'amount_requested' => 'required|numeric|min:100',
            'term_months'      => 'required|integer|min:1',
            'collateral_info'  => 'nullable|string',
        ]);

        $interestRate = 10.00; // ავტომატური 10%
        $amountRequested = $validated['amount_requested'];
        $totalToRepay = $amountRequested + ($amountRequested * ($interestRate / 100));

        $loan = Loan::create([
            'borrower_id'           => $request->user()->id,
            'title'                 => $validated['title'],
            'description'           => $validated['description'],
            'amount_requested'      => $amountRequested,
            'interest_rate'         => $interestRate,
            'total_amount_to_repay' => $totalToRepay,
            'term_months'           => $validated['term_months'],
            'collateral_info'       => $validated['collateral_info'] ?? null,
            'status'                => 'active',
        ]);

        return response()->json([
            'message' => 'სესხის განაცხადი წარმატებით შეიქმნა',
            'loan'    => $loan,
        ], 201);
    }

    /**
     * სესხის დეტალების ნახვა
     */
    public function show($id)
    {
        $loan = Loan::with(['borrower:id,name,email', 'investments.investor:id,name'])->find($id);

        if (!$loan) {
            return response()->json(['message' => 'სესხი ვერ მოიძებნა'], 404);
        }

        return response()->json($loan);
    }
}