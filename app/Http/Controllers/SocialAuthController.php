<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        // Use the given provider (e.g., google, facebook) and redirect user to its login page
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider)
    {
        // Get the authenticated user's information from the provider
        $socialUser = Socialite::driver($provider)->stateless()->user();

         // Try to find the user in the database using provider ID or email
        $user = User::where($provider . '_id', $socialUser->getId())
                    ->orWhere('email', $socialUser->getEmail())
                    ->first();

                      // If user doesn't exist, create a new user record
        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                $provider . '_id' => $socialUser->getId(),
                'password' => bcrypt(Str::random(16)),
            ]);
        }

          // Log the user into your Laravel application
        Auth::login($user);

          // Redirect the user to the dashboard or intended page
        return redirect()->intended('product/index');
    }
}
