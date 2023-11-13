<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}}'s Profile">
  <div class="list-group">
		@foreach($userPosts as $userPost)
      <x-post :userPost="$userPost"  hideAuthor="true"/>
		@endforeach
	</div>
</x-profile>