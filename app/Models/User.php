<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'id_number',
        'role',
        'agreed_to_terms_and_privacy',
        'terms_agreed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'agreed_to_terms_and_privacy' => 'boolean',
            'terms_agreed_at' => 'datetime',
        ];
    }

    // კავშირი სესხებთან (თუ მსესხებელია)
    public function loans()
    {
        return $this->hasMany(Loan::class, 'borrower_id');
    }

    // კავშირი ინვესტიციებთან (თუ ინვესტორია)
    public function investments()
    {
        return $this->hasMany(Investment::class, 'investor_id');
    }

    // კავშირი ხელშეკრულებებთან
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}