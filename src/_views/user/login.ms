{{% BLOCKS }}
{{< layout }}
{{$ title }}Logg inn{{/ title }}

{{$ content}}



<h1>Logg inn</h1>

<form action="{{PATH}}" method="post">

	<input type="hidden" name="url" value="{{_post.url}}">
	
	<p><input type="email" name="email" placeholder="Epost" value="{{_post.email}}" autocomplete="email" autofocus>
	
	<p><input type="password" name="password" placeholder="Passord">
	
	<p>
		<button class="icon" type="submit">{{#svg}}login{{/svg}}</button>
		&nbsp;
		<a class="small" href="user/reset">Glemt passord</a>
	</p>
</form>



{{/ content}}
{{/ layout }}
