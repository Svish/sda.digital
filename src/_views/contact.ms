{{% BLOCKS }}
{{< layout }}
{{$ title }}Kontakt oss{{/ title }}

{{$ content}}



<h1>Kontakt oss</h1>

<form action="{{PATH}}" method="post">
	
	<p><input {{#errors.from}}class="error"{{/errors.from}} type="email" name="from" placeholder="Din epost" value="{{_post.from}}" required>
	{{#errors.from}}<span class="field error">{{.}}</span>{{/errors.from}}
	
	<p><input {{#errors.subject}}class="error"{{/errors.subject}} type="text" name="subject" placeholder="Emne" value="{{_post.subject}}" required>
	{{#errors.subject}}<span class="field error">{{.}}</span>{{/errors.subject}}
	
	<p><textarea class="full-width {{#errors.message}}error{{/errors.message}}" name="message" placeholder="Melding" required>{{_post.message}}</textarea>
	{{#errors.message}}<span class="field error">{{.}}</span>{{/errors.message}}
	
	<p><button class="large" type="submit">{{#svg}}send{{/svg}} Send</button>
</form>



{{/ content}}
{{/ layout }}
