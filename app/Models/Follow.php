<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model {
  use HasFactory;

  // Get users following the current user and set them to belong to the current user.
  // user_id in the follow method is the user doing the following
  public function userDoingTheFollowing() {
    return $this->belongsTo(User::class, 'user_id');
  }

  // We get the user being followed by current user and set them belongin to that user.
  // followeduser in the Follow model is the user being followed.
  public function  userBeingFollowed() {
    return $this->belongsTo(User::class, 'followeduser');
  }
}
