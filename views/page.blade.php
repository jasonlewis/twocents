@if($page->title)
	<h2 class="title">{{ $page->title }}</h2>
@endif

{{ $page->body }}