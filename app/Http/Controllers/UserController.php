<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;

class UserController extends Controller {

  public function storeAvatar(Request $request) {
    $request->validate([
      'avatar' => 'required|image|max:3000'
    ]);

    // Get Current user
    $user = auth()->user();

    // Generate filename
    $filename = $user->id . '-' . uniqid() . '.jpg';

    // Modify/resize image before storing
    $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');

    // Store Image
    Storage::put('public/avatars/' . $filename, $imgData);

    // Delete old/existing avatar
    $oldAvatar = $user->avatar;

    // Update and save in Database
    $user->avatar = $filename;
    $user->save();

    // Only delete avatar if avatar is not default image.
    if ($oldAvatar != "/fallback-avatar.jpg") {
      // We replace /storage/ with public/ from $oldavatar then delete that oldAvatar file
      Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
    }

    // Redirect back to manage-avatar form
    return back()->with('Avatar Saved Successfully!');
  }

  public function showAvatarForm() {
    return view('avatar-form');
  }

  public function visitDashboard() {
    return 'Welcome to the dashboard';
  }

  public function profile(User $user) {
    $currentlyFollowing = 0;

    if (auth()->check()) {
      $currentlyFollowing = Follow::where([
        ['user_id', '=', auth()->user()->id],
        ['followeduser', '=', $user->id]
      ])->count();
    }

    return view(
      'profile-posts',
      [
        'currentlyFollowing' => $currentlyFollowing,
        'username' => $user->username,
        'avatar' => $user->avatar,
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
