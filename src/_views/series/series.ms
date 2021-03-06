{{% BLOCKS }}
{{< layout }}
{{$ title }}{{series.title}}{{/ title }}

{{$ content}}


{{#series}}

	<h1>
		<a href="{{url}}">
			{{#svg}}list{{/svg}}
			{{title}}
		</a>
	</h1>

	
	{{> edit/series}}


{{#role.admin}}
<pre>// TODO
* Series media player
* Auto-sort by ID3 track numbers
</pre>
{{/role.admin}}

	{{> list/filter}}
	{{> list/content}}

	<section class="fresh" data-bind="html: fresh"></section>

	<section data-bind="visible: ! item()">
		<hr>
		{{> list/person}}
		{{> list/location}}
	</section>

{{/series}}


{{/ content}}
{{/ layout }}
