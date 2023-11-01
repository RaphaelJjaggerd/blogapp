<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {
  use HasFactory;

  protected $fillable = ['title', 'body', 'user_id'];

  public function user() {
    // Return a relationship between post and user
    // We are saying a blog post belongs to a user.
    return $this->belongsTo(User::class, 'user_id');
  }
}
