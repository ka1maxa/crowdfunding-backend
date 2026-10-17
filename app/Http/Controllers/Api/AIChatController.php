<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AIChatController extends Controller
{
    /**
     * AI ჩატის ენდპოინტი
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message'    => 'required|string',
            'session_id' => 'nullable|string',
        ]);

        $userMessage = mb_strtolower($validated['message']);
        $user = $request->user('sanctum'); // ავტორიზებულია თუ სტუმარი

        // 1. შევამოწმოთ ითხოვს თუ არა ინვესტირებას/სტარტაპის განთავსებას/დაკავშირებას
        if (
            Str::contains($userMessage, ['ინვესტირება', 'ინვესტიცია', 'სტარტაპი', 'დაკავშირება', 'კონტაქტი', 'დარეკვა', 'სესხის აღება', 'თანხის ჩადება'])
        ) {
            $aiResponse = "მშვენიერია! რადგან ტრანზაქციები და ხელშეკრულებების საბოლოო გაფორმება ხდება ინდივიდუალურად (ოფლაინ), გთხოვთ გადახვიდეთ **საკონტაქტო გვერდზე** და შეავსოთ ფორმა, ან დაგვიკავშირდეთ, რათა ჩვენი გუნდი დაგეხმაროთ პროცესის დასრულებაში.";
        } 
        // 2. ზოგადი კითხვები პლატფორმაზე
        else if (Str::contains($userMessage, ['რა არის', 'როგორ მუშაობს', 'წესები', 'რისკები', 'პლატფორმა'])) {
            $aiResponse = "ჩვენი პლატფორმა წარმოადგენს Crowdfunding / P2P სისტემის დემო ვერსიას. აქ შეგიძლიათ გაეცნოთ სტარტაპების განაცხადებს, ხელშეკრულებების ნიმუშებსა და AI ანალიტიკას. დეტალური ინფორმაციისთვის შეგიძლიათ ეწვიოთ საგანმანათლებლო გვერდს.";
        } 
        // 3. სხვა ზოგადი პასუხი
        else {
            $aiResponse = "მე ვარ პლატფორმის AI ასისტენტი. შემიძლია გიპასუხოთ ზოგად კითხვებზე. თუ გსურთ ინვესტირება ან სტარტაპის განთავსება, გთხოვთ ისარგებლოთ საკონტაქტო ფორმით.";
        }

        // ისტორიის შენახვა ბაზაში
        $chat = ChatHistory::create([
            'user_id'    => $user ? $user->id : null,
            'session_id' => $validated['session_id'] ?? Str::uuid(),
            'message'    => $validated['message'],
            'response'   => $aiResponse,
        ]);

        return response()->json([
            'message'  => $chat->message,
            'response' => $chat->response,
            'session_id' => $chat->session_id,
        ]);
    }
}