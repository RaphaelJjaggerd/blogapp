<a href="/post/{{$userPost->id}}" class="list-group-item list-group-item-action">
  <img class="avatar-tiny" src="{{$userPost->user->avatar}}" />
  <strong> {{$userPost->title}} </strong>
  <span class="text-muted small"> 
    @if(!isset($hideAuthor))  
    by {{$userPost->user->username}} 
    @endif
    on {{$userPost->created_at->format('j/n/Y')}}</span>
</a>