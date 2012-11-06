<div id="articles">

	<div class="article">

		<h2 class="title"><a href="{{ URL::to_route('twocents: article', array($article->slug)) }}">{{ $article->title }}</a></h2>

		<div class="meta">
			By {{ $article->author->name }} on <span class="date">{{ date('j F, Y', strtotime($article->date)) }}</span>
		</div>

		<p>
			{{ $article->body }}
		</p>
	</div>

	@if(Config::get('twocents::twocents.disqus.enabled'))
		<div id="disqus_thread"></div>
		<script type="text/javascript">
		    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
		    var disqus_shortname = '{{ Config::get('twocents::twocents.disqus.shortname') }}';

		    /* * * DON'T EDIT BELOW THIS LINE * * */
		    (function() {
		        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
		        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
		        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		    })();
		</script>
	@endif

</div>