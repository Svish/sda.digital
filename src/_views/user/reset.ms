{{% BLOCKS }}
{{< layout }}
{{$ title }}Glemt passord{{/ title }}

{{$ content}}



<h1>Glemt passord</h1>

<form action="{{PATH}}" method="post">
	
	<p><input type="email" name="email" placeholder="Epost" value="{{_post.email}}{{_get.email}}" autocomplete="email" autofocus>

	<p>
		<button class="icon" type="submit">{{#svg}}login{{/svg}}</button>
	</p>
</form>



{{/ content}}
{{/ layout }}
