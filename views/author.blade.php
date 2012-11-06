<div class="section">
	<h2>About The Author</h2>

	<dl>
		<dt>Name</dt>
		<dd>{{ $author->name }}</dd>

		@if($author->homepage)
			<dt>Homepage</dt>
			<dd>{{ HTML::link($author->homepage, $author->homepage) }}</dd>
		@endif

		@if($author->twitter)
			<dt>Twitter</dt>
			<dd>{{ HTML::link('http://twitter.com/'.$author->twitter, $author->twitter) }}</dd>
		@endif

		@if($author->github)
			<dt>GitHub</dt>
			<dd>{{ HTML::link('http://github.com/'.$author->github, $author->github) }}</dd>
		@endif

		@if($author->location)
			<dt>Location</dt>
			<dd>{{ $author->location }}</dd>
		@endif
	</dl>
	
	@if($author->gravatar)
		{{ HTML::image($author->gravatar, $author->name . "'s Gravatar", array('class' => 'gravatar')) }}
	@endif

	@if($author->bio)
		<p>
			{{ $author->bio }}
		</p>
	@endif
</div>