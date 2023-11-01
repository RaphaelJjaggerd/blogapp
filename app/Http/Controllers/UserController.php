<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UserController extends Controller {

  public function visitDashboard() {
    return 'Welcome to the dashboard';
  }

  public function profile(User $user) {
    return view(
      'profile-posts',
      [
        'username' => $user->username,
        'user_posts' => $user->userPosts()->latest()->get(),
        'posts_count' => $user->userPosts()->count()
      ]
    );
  }

  public function logout() {
    auth()->logout();
    return redirect('/')->with('success', 'You are logged out!');
  }

  public function showCorrectHomepage() {
    if (auth()->check()) {
      return view('homepage-feed');
    } else {
      return view('homepage');
    }
  }

  public function login(Request $request) {
    $incomingFields = $request->validate([
      'loginusername' => 'required',
      'loginpassword' => 'required'
    ]);

    if (
      auth()->attempt(
        [
          'username' => $incomingFields['loginusername'],
          'password' => $incomingFields['loginpassword']
        ]
      )
    ) {
      $request->session()->regenerate();
      return redirect('/')->with('success', 'You have successfully logged in!');
    } else {
      return redirect('/')->with('failure', 'Invalid login.');
    }
  }

  public function register(Request $request) {
    // Validate incoming requests
    $incomingFields = $request->validate([
      'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
      'email' => ['required', 'email', Rule::unique('users', 'email')],
      'password' => ['required', 'min:6', 'confirmed']
    ]);

    $incomingFields['password'] = bcrypt($incomingFields['password']);

    //Save values in the database and store values in $user.
    $user = User::create($incomingFields);

    // Login user.
    auth()->login($user);

    // Redirect user.
    return redirect('/')->with('success', 'Account Created successfully!');
  }
}
