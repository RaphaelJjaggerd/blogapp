  <div class="list-group">
		@foreach($userPosts as $userPost)
      <x-post :userPost="$userPost"  hideAuthor="true"/>
		@endforeach
	</div>