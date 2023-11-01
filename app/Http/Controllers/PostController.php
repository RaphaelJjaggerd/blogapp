<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller {
  public function actuallyUpdate(Post $post, Request $request) {
    // Validate fields
    $incomingFields = $request->validate([
      'title' => 'required',
      'body' => 'required'
    ]);

    // Strip malicious tags if any
    $incomingFields['title'] = strip_tags($incomingFields['title']);
    $incomingFields['body'] = strip_tags($incomingFields['body']);

    // Update the Post
    $post->update($incomingFields);

    // Redirects user to the previous url they came from
    // return back()->with('success', 'Post Successfully Updated!');
    return redirect('/post/' . $post->id)->with('success', 'Post Successfully Updated!');
  }


  public function showEditForm(Post $post) {
    return view('edit-post', ['post' => $post]);
  }


  public function delete(Post $post) {
    // Delete Post
    $post->delete();

    // Redirect user
    return redirect('/profile/' . auth()->user()->username)->with('success', 'Post successfully deleted.');
  }

  public function viewSinglePost(Post $post) {
    $post['body'] = strip_tags(Str::markdown($post->body), '<p><ul><ol><li><strong><em><h1><h2><h3><br>');
    return view('single-post', ['post' => $post]);
  }

  public function storeNewPosts(Request $request) {
    // Validate input
    $incomingFields = $request->validate([
      'title' => 'required',
      'body' => 'required',
    ]);

    // Strip malicious tags if any
    $incomingFields['title'] = strip_tags($incomingFields['title']);
    $incomingFields['body'] = strip_tags($incomingFields['body']);

    // Add the foreign_id if any.
    $incomingFields['user_id'] = auth()->id();

    // Store new post in the Database and in memory.
    $newPost = Post::create($incomingFields);

    return redirect("/post/{$newPost->id}")->with('success', 'New post successfully created!');
  }


  public function showCreateForm() {

    return view('create-post');
  }
}
