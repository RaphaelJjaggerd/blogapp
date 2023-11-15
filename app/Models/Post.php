<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model {

  use Searchable;
  use HasFactory;

  protected $fillable = ['title', 'body', 'user_id'];

  public function user() {
    // Return a relationship between post and user
    // We are saying a blog post belongs to a user.
    return $this->belongsTo(User::class, 'user_id');
  }

  // spells out what shoud be searchable on the databse row
  public function toSearchableArray() {
    return [
      'title' => $this->title,
      'body' => $this->body
    ];
  }
}
