<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller {
  public function createFollow(User $user) {
    // You cannot follow yourself
    if ($user->id == auth()->user()->id) {
      return back()->with('failure', 'You cannot follow yourself.');
    }

    // You cannot follow someone you are already following.
    $existCheck = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
    if ($existCheck) {
      return back()->with('failure', 'You are already following this user.');
    }

    // Create a new follow
    $newFollow = new Follow;
    $newFollow->user_id = auth()->user()->id;  // The currenly logged in user that is creating the follow.
    $newFollow->followeduser =  $user->id;  // The person being followed. $user is coming from the url
    $newFollow->save();

    return back()->with('success', 'User successfully followed');
  }
  public function removeFollow(User $user) {
    Follow::where([
      ['user_id', '=', auth()->user()->id],
      ['followeduser', '=', $user->id]
    ])->delete();

    return back()->with('success', 'User successfully unfollowed!');
  }
}
