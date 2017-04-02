{{% ACCESS login }}
{{% BLOCKS }}
{{< layout }}
{{$ title }}Admin{{/ title }}

{{$ content}}

<div class="menu no-select">

	<section>
		{{#role.editor}}
		<a class="frame fresh" href="manage/content/fresh-dirs">
			{{#svg}}add{{/svg}}<br>
			Legg til nytt innhold
		</a>
		<a class="frame" href="content/my-fresh">
			{{#svg}}menu{{/svg}}<br>
			Nylig lagt til
		</a>
		{{/role.editor}}

		{{#role.admin}}
		<a class="frame" href="admin/issues">
			{{#svg}}error{{/svg}}<br>
			Problemer
		</a>
		{{/role.admin}}
	</section>

	<section>
		{{#role.admin}}
		<a class="frame" href="admin/users">
			{{#svg}}people{{/svg}}<br>
			Alle
		</a>
		{{/role.admin}}
		{{#role.login}}
		<a class="frame" href="user/me">
			{{#svg}}person{{/svg}}<br>
			Meg
		</a>
		<a class="frame" href="user/logout">
			{{#svg}}logout{{/svg}}<br>
			Logg ut
		</a>
		{{/role.login}}
	</section>
</div>

{{/ content}}
{{/ layout }}
