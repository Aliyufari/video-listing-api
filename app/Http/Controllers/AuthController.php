<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return $this->response(
                false,
                'Invalid Credentials',
                null,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $user = User::firstWhere('email', $credentials['email']);
        $token = $user->createToken(
            'token for ' . $user->email,
            ['*'],
            now()->addDay()
        )->plainTextToken;

        return $this->response(
            true,
            'Athenticated',
            $token,
            'token'
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->response(
            true,
            'Logged Out Successfully',
            null,
        );
    }
}
