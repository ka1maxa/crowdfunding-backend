<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Investment;
use App\Models\Contract;
class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_id',
        'title',
        'description',
        'amount_requested',
        'interest_rate',
        'total_amount_to_repay',
        'amount_raised',
        'term_months',
        'collateral_info',
        'status',
        'ai_summary',
        'ai_risk_score',
    ];

    protected $casts = [
        'ai_risk_score' => 'array',
        'amount_requested' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_amount_to_repay' => 'decimal:2',
        'amount_raised' => 'decimal:2',
    ];

    // მსესხებელთან (User) კავშირი
    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    // ინვესტიციებთან კავშირი
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    // ხელშეკრულებებთან კავშირი
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}