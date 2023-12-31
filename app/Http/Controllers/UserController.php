<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Events\OurExampleEvent;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;

class UserController extends Controller {



  public function visitDashboard() {
    return 'Welcome to the dashboard';
  }

  public function showCorrectHomepage(User $user) {
    if (auth()->check()) {
      return view(
        'homepage-feed',
        [
          'userPosts' => auth()->user()->feedPosts()->latest()->paginate(4),
        ]
      );
    } else {
      $postCount = Cache::remember('postCount', 20, function () {
        return Post::count();
      });
      return view(
        'homepage',
        [
          'postCount' => $postCount,
        ]
      );
    }
  }


  // PROFILE
  public function profile(User $user) {
    $this->getSharedData($user);

    return view(
      'profile-posts',
      [
        'userPosts' => $user->userPosts()->latest()->get(),
      ]
    );
  }

  public function profileRaw(User $user) {
    $userPosts = $user->userPosts()->latest()->get();
    return response()->json(
      [
        'theHTML' => view('profile-posts-only', ['userPosts' => $userPosts])->render(),
        'docTitle' => $user->username . "'s Profile"
      ]
    );
  }

  public function profileFollowers(User $user) {
    $this->getSharedData($user);
    return view(
      'profile-followers',
      [
        'followers' => $user->followers()->latest()->get(),
      ]
    );
  }

  public function profileFollowersRaw(User $user) {
    $followers = $user->followers()->latest()->get();
    return response()->json(
      [
        'theHTML' => view('profile-followers-only', ['followers' => $followers])->render(),
        'docTitle' => $user->username . "'s Followers"
      ]
    );
  }

  public function profileFollowing(User $user) {
    $this->getSharedData($user);

    return view(
      'profile-following',
      [
        'following' => $user->followingTheseUsers()->latest()->get(),
      ]
    );
  }

  public function profileFollowingRaw(User $user) {
    $following = $user->followingTheseUsers()->latest()->get();
    return response()->json(
      [
        'theHTML' => view('profile-following-only', ['following' => $following])->render(),
        'docTitle' => "Who" . $user->username . "Follows"
      ]
    );
  }


  // AUTHENTICATION ///////////////////////////////////////////
  public function logout() {
    event(new OurExampleEvent(
      [
        'username' => auth()->user()->username,
        'action' => 'Logout'
      ]
    ));
    auth()->logout();
    return redirect('/')->with('success', 'You are logged out!');
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
      event(new OurExampleEvent(
        [
          'username' => auth()->user()->username,
          'action' => 'Login'

        ]
      ));
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

  public function loginAPI(Request $request) {
    $incomingFields = $request->validate([
      'username' => 'required',
      'password' => 'required',
    ]);

    if (auth()->attempt($incomingFields)) { // Only returns true if password and username are valid.
      $user = User::where('username', $incomingFields['username'])->first();
      $token = $user->createToken('ourapptoken')->plainTextToken;
      return $token;
    }

    return 'Not Valid';
  }

  // MISCALLENEOUS 
  private function getSharedData($user) {
    $currentlyFollowing = 0;

    if (auth()->check()) {
      $currentlyFollowing = Follow::where([
        ['user_id', '=', auth()->user()->id],
        ['followeduser', '=', $user->id]
      ])->count();
    }

    View::share('sharedData', [
      'currentlyFollowing' => $currentlyFollowing,
      'username' => $user->username,
      'avatar' => $user->avatar,
      'postsCount' => $user->userPosts()->count(),
      'followerCount' => $user->followers()->count(),
      'followingCount' => $user->followingTheseUsers()->count()
    ]);
  }
}
