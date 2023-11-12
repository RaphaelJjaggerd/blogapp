<x-profile :sharedData="$sharedData">
  <div class="list-group">
		@foreach($userPosts as $userPost)
		<a href="/post/{{$userPost->id}}" class="list-group-item list-group-item-action">
			<img class="avatar-tiny" src="{{$userPost->user->avatar}}" />
			<strong> {{$userPost->title}} </strong> on {{$userPost->created_at->format('j/n/Y')}}
		</a>
		@endforeach
	</div>
</x-profile>