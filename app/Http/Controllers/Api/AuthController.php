<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan salah.'],
            ]);
        }

        // Revoke all existing tokens for the user (optional, for single active token)
        // $user->tokens()->delete();

        // Create a new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user, // Consider using a UserResource here for consistent output
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Log out the authenticated user by revoking their current token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        /** @var User $user */ // Ini membantu Intelephense mengenali $user sebagai instance User
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            /** @var PersonalAccessToken $token */ // Ini membantu Intelephense mengenali $token
            $token = $user->currentAccessToken();
            $token->delete(); // Ini seharusnya tidak undefined jika $token adalah PersonalAccessToken
        }

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get the authenticated user's data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json([
            'message' => 'User data retrieved successfully',
            'data' => $request->user() // Consider using a UserResource here
        ]);
    }

    /**
     * Send a password reset link to the given email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Link reset password telah dikirim ke email Anda.'], 200);
        }

        return response()->json(['message' => 'Tidak dapat mengirim link reset password ke email ini.'], 500);
    }

    /**
     * Reset the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password Anda berhasil direset.'], 200);
        }

        return response()->json(['message' => 'Token reset password tidak valid atau kedaluwarsa.'], 401);
    }
}
