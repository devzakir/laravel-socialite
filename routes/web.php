<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


use Laravel\Socialite\Facades\Socialite;

Route::get('/auth/redirect', function () {
    $app = request()->app;

    if ($app == "github") {
        return Socialite::driver('github')->redirect();
    } elseif ($app == "facebook") {
        return Socialite::driver('facebook')->redirect();
    } elseif ($app == "google") {
        return Socialite::driver('google')->redirect();
    } else {
        return redirect()->back();
    }
})->name('auth.redirect');

Route::get('/callback-url', function () {
    $app = request()->app;
    $user = null;

    if ($app == "github") {
        $user = Socialite::driver('github')->user();
    } elseif ($app == "facebook") {
        $user = Socialite::driver('facebook')->user();
    } elseif ($app == "google") {
        $user = Socialite::driver('google')->user();
    } else {
        return redirect()->back();
    }

    if ($user) {
        $data = User::firstOrCreate([
            'email' => $user->email
        ], [
            'name' => $user->name,
            'password' => bcrypt(Str::random(24))
        ]);

        Auth::login($data, true);
        return redirect('/dashboard');
    }

    // $token = $user->token;
    // $user = Socialite::driver('github')->userFromToken($token);

    // // OAuth 2.0 providers...
    // $token = $user->token;

    // $refreshToken = $user->refreshToken;
    // $expiresIn = $user->expiresIn;

    // // OAuth 1.0 providers...
    // $token = $user->token;
    // $tokenSecret = $user->tokenSecret;

    // // All providers...
    // $user->getId();
    // $user->getNickname();
    // $user->getName();
    // $user->getEmail();
    // $user->getAvatar();
});

require __DIR__ . '/auth.php';
