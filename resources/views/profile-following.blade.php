<x-profile :avatar="$avatar" :username="$username" :currentlyFollowing="$currentlyFollowing" :postCount="$postCount">
  	<div class="list-group">
		@foreach($user_posts as $user_post)
		<a href="/post/{{$user_post->id}}" class="list-group-item list-group-item-action">
			<img class="avatar-tiny" src="{{$avatar}}" />
			<strong> {{$user_post->title}} </strong> on {{$user_post->created_at->format('j/n/Y')}}
		</a>
		@endforeach

	</div>
</x-profile>