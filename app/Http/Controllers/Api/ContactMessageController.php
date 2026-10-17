<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * საკონტაქტო ფორმიდან შეტყობინების მიღება და შენახვა
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'subject'      => 'required|string|max:255',
            'message'      => 'required|string',
        ]);

        $message = ContactMessage::create([
            'full_name'    => $validated['full_name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'subject'      => $validated['subject'],
            'message'      => $validated['message'],
            'status'       => 'new',
        ]);

        return response()->json([
            'message' => 'თქვენი შეტყობინება წარმატებით გაიგზავნა. ჩვენი გუნდი მალე დაგიკავშირდებათ!',
            'data'    => $message,
        ], 201);
    }

    /**
     * შემოსული შეტყობინებების სია (ადმინისთვის)
     */
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'წვდომა უარყოფილია.'], 403);
        }

        $messages = ContactMessage::orderBy('created_at', 'desc')->get();

        return response()->json($messages);
    }
}