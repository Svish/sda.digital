{{% BLOCKS }}
{{< layout }}
{{$ title }}{{status}} {{title}}{{/ title }}

{{$ content}}

	{{#role.admin}}
		{{{debug}}}
		{{{stack}}}
	{{/role.admin}}

	<p class="center">
		<img class="frame" src="https://http.cat/{{status}}">


{{/ content}}
{{/ layout }}
