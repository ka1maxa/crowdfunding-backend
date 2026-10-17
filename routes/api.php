<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\InvestmentController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\AIChatController;

/*
|--------------------------------------------------------------------------
| საჯარო მარშრუტები (Public Routes)
|--------------------------------------------------------------------------
*/
// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Loans (ყველას შეუძლია ნახვა)
Route::get('/loans', [LoanController::class, 'index']);
Route::get('/loans/{id}', [LoanController::class, 'show']);

// Contact Form (ყველას შეუძლია შეტყობინების გაგზავნა)
Route::post('/contact', [ContactMessageController::class, 'store']);

// AI Chatbot (სტუმრებისთვის და ავტორიზებულებისთვის)
Route::post('/ai/chat', [AIChatController::class, 'chat']);


/*
|--------------------------------------------------------------------------
| დაცული მარშრუტები (Protected Routes - Requires Bearer Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // User Profile & Logout
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Loans (სესხის განაცხადის შექმნა)
    Route::post('/loans', [LoanController::class, 'store']);

    // Investments (ინვესტირება და ჩემი ინვესტიციები)
    Route::post('/investments', [InvestmentController::class, 'store']);
    Route::get('/my-investments', [InvestmentController::class, 'myInvestments']);

    // Contracts (ხელშეკრულებები)
    Route::post('/contracts/generate', [ContractController::class, 'generate']);
    Route::post('/contracts/{id}/sign-demo', [ContractController::class, 'signDemo']);
    Route::get('/my-contracts', [ContractController::class, 'myContracts']);

    // Admin Panel
    Route::get('/admin/contact-messages', [ContactMessageController::class, 'index']);
});