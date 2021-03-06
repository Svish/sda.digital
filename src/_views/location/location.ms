{{% BLOCKS }}
{{< layout }}
{{$ title }}{{location.name}}{{/ title }}

{{$ content}}


{{#location}}

	<h1>
		<a href="{{url}}">
			{{#svg}}place{{/svg}}
			{{name}}
		</a>
	</h1>
	
	{{> edit/location}}

	<p data-bind="visible: ! item()">
		<a class="map-link" title="Gå til Google Maps" href="https://maps.google.com/?q={{address | f.encodeAddress}}">
			{{> map}}
		</a>
	</p>

	<section class="meta flex" data-bind="visible: ! item()">
		<div>
			<h2>Adresse</h2>
			<address>{{address}}</address>
		</div>
		{{#website}}
		<div>
			<h2>Nettside</h2>
			<p><a href="{{website}}" rel="nofollow">{{website | f.url}}</a>
		</div>
		{{/website}}
	</section>

	<section data-bind="visible: ! item()">
		{{> list/filter}}
		{{> list/series}}
		{{> list/person}}
		{{> list/content}}
	</section>

{{/location}}


{{/ content}}
{{/ layout }}
