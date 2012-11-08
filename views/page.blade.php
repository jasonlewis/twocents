<div id="articles">
	<div class="article">
		@if($page->title)
			<h2 class="title">{{ $page->title }}</h2>
		@endif

		<div class="body">
			{{ $page->body }}
		</div>
	</div>
</div>