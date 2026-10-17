<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * მომხმარებლის რეგისტრაცია
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                        => 'required|string|max:255',
            'email'                       => 'required|string|email|max:255|unique:users',
            'phone_number'                => 'required|string|max:20',
            'id_number'                   => 'required|string|max:50|unique:users',
            'password'                    => 'required|string|min:8|confirmed',
            'role'                        => 'required|in:investor,borrower',
            'agreed_to_terms_and_privacy' => 'required|boolean|accepted', // უნდა იყოს true (1)
        ]);

        $user = User::create([
            'name'                        => $validated['name'],
            'email'                       => $validated['email'],
            'phone_number'                => $validated['phone_number'],
            'id_number'                   => $validated['id_number'],
            'password'                    => Hash::make($validated['password']),
            'role'                        => $validated['role'],
            'agreed_to_terms_and_privacy' => true,
            'terms_agreed_at'             => now(),
        ]);

        // Sanctum API Token-ის გენერაცია
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'მომხმარებელი წარმატებით დარეგისტრირდა',
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);
    }

    /**
     * სისტემაში შესვლა (Login)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['მითითებული მონაცემები არასწორია.'],
            ]);
        }

        // ძველი ტოკენების წაშლა და ახლის გაცემა
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'ავტორიზაცია წარმატებით გაიარეთ',
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    /**
     * ავტორიზებული მომხმარებლის პროფილის წაკითხვა
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * სისტემიდან გამოსვლა (Logout)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'სისტემიდან წარმატებით გამოხვედით',
        ]);
    }
}