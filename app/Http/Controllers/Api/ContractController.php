<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Loan;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * დემო ხელშეკრულების შექმნა
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'loan_id'       => 'required|exists:loans,id',
            'contract_type' => 'required|in:borrower_agreement,investor_agreement',
        ]);

        $contract = Contract::create([
            'loan_id'            => $validated['loan_id'],
            'user_id'            => $request->user()->id,
            'contract_type'      => $validated['contract_type'],
            'status'             => 'generated',
            'extracted_metadata' => [
                'notice' => 'ეს არის დემო ხელშეკრულება რეალური ციფრული ხელმოწერის გარეშე.',
                'generated_at' => now()->toDateTimeString(),
            ],
        ]);

        return response()->json([
            'message'  => 'დემო ხელშეკრულება წარმატებით შეიქმნა',
            'contract' => $contract,
        ], 201);
    }

    /**
     * დემო ხელმოწერა (სიმულაცია - სტატუსის შეცვლა signed_demo-ზე)
     */
    public function signDemo(Request $request, $id)
    {
        $contract = Contract::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $contract->update([
            'status' => 'signed_demo',
        ]);

        return response()->json([
            'message'  => 'ხელშეკრულებაზე დემო ხელმოწერა წარმატებით დაფიქსირდა!',
            'contract' => $contract,
        ]);
    }

    /**
     * მომხმარებლის ხელშეკრულებების სია
     */
    public function myContracts(Request $request)
    {
        $contracts = Contract::with('loan')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($contracts);
    }
}