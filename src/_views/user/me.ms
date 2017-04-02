{{% BLOCKS }}
{{< layout }}
{{$ title }}Meg{{/ title }}

{{$ content}}



<h1>Meg</h1>

<form action="{{PATH}}" method="post">
	
	<p>
		<input {{#errors.name}}class="error"{{/errors.name}} type="text" name="name" value="{{me.name}}" placeholder="Ditt navn" autocomplete="name" required>
		{{#errors.name}}<span class="field error">{{.}}</span>{{/errors.name}}
	</p>

	<p>
		<input {{#errors.email}}class="error"{{/errors.email}} type="email" name="email" value="{{me.email}}" placeholder="Din epost" autocomplete="email" required>
	{{#errors.email}}<span class="field error">{{.}}</span>{{/errors.email}}
	</p>
	
	<p>
		<input {{#errors.password}}class="error"{{/errors.password}} type="text" name="password" placeholder="Passord, om du vil bytte" value="{{me.password}}" autocomplete="off">
		{{#errors.password}}<span class="field error">{{.}}</span>{{/errors.password}}
	</p>

	<p><button class="icon" type="submit">{{#svg}}save{{/svg}}</button>
</form>



{{/ content}}
{{/ layout }}
