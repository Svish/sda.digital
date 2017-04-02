{{% BLOCKS }}
{{< layout }}
{{$ title }}{{content.title}}{{/ title }}

{{$ content}}


{{#content}}

	<h1>
		<a href="{{url}}">
			{{title}}
		</a>
	</h1>
	<div class="meta">
		
		<div><b>Sted:</b>
			{{#location}}<a href="{{url}}">{{name}}</a>{{/location}}
			{{^location}}Ukjent{{/location}}
		</div>
		<div><b>Tatt opp:</b> {{time | f.flex | ucfirst}}</div>
		<div><b>Lagt til:</b> <time datetime="{{created | f.iso}}">{{created | f.full}}</time></div>
	</div>

	{{> edit/content}}


{{#role.admin}}
<pre>// TODO
* Add content to series
* Media player
</pre>
{{/role.admin}}

	{{#md}}{{summary}}{{/md}}


	{{> list/file}}


	{{> list/person}}
	{{> list/series}}

{{/content}}


{{/ content}}
{{/ layout }}
