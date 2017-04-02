{{% ACCESS editor }}
{{% BLOCKS }}
{{< layout }}
{{$ title }}Nytt innhold{{/ title }}

{{$ content}}


<script defer type="text/javascript" src="js/dnd.js"></script>
<script defer type="text/javascript" src="js/manage-content-fresh-content.js"></script>


<h1>Nytt innhold…
	<span data-bind="text: path"></span>
</h1>

<details>
	<summary>Jo mer informasjon du fyller ut her, jo lettere blir innhold å finne for brukere i etterkant.</summary>
	
	<div>
		<p>Husk at <i>innhold</i> kan inkludere ulike <i>filer</i>. For eksempel lyd-versjon, video-versjon og tilhørende powerpoint/pdf. Filer med samme navn har automatisk blitt gruppert, men kan flyttes på (Dra fra {{#svg}}menu{{/svg}} til {{#svg}}insert{{/svg}}).

		<h2>Sted</h2>
		<p>I motsetning til personer, må nye steder foreløpig <a href="manage/locations">legges til</a> <em>før</em> de kan brukes her.</p>

		<h2>ID3-tags</h2>
		<p>Bruk {{#svg}}magnifier{{/svg}} for å hente info fra en fils ID3-tag.</p>
		<ul>
			<li>Hvis det er flere "artister" vil første settes som <i>Taler</i>, og neste som <i>Oversetter</i>.
			<li>Hvis "comment" inneholder noe som ser ut som en tid, så vil den plukkes ut og brukes som <i>Når</i>; ellers brukes "year".
			<li>Ellers brukes "title" som <i>Tittel</i>, og "comment" som <i>Sammendrag</i>.
		</ul>

		<h2>Tenkt fremgangsmåte:</h2>
		<ol>
			<li>Sjekk at filer er gruppert riktig (drag'n'drop if not)
			<li>Hent info fra tag.
			<li>Fyll ut resten/korriger.
			<li>Lagre.
			<li>Eventuelt, til slutt, legg filene til i en ny/eksisterende serie.
		</ol>
	</div>
</details>

<hr>

{{> waiting }}

<section class="gone" id="workspace" data-bind="event: {dragstart: ondragstart, drop: ondrop}, css: {gone: !content().length}">
	<!-- ko template: { name: 'content-tmpl', foreach: content, beforeRemove: beforeRemove } -->
	<!-- /ko -->
</section>

<section class="gone" data-bind="css: {gone: content().length}">
	<p>Da var det ikke mer å legge til i denne mappen. 🎉
	<ul>
		<li>Gå til en <a href="{{_path}}/../fresh-dirs">annen mappe</a>.
		<li>Se hva du <a href="content/my-fresh">nylig har lagt til</a>, og Organiser det <a href="series/index">i serier</a>.
		<li>Ha en fin dag videre. 🐱🏍
	</ul>
</section>

<script type="text/html" id="content-tmpl">
	
	<div class="content">
		<h2 data-bind="text: title"></h2>
		<div class="flex">
			<p>
				<input required type="text" placeholder="Tittel" data-bind="textInput: title, css: {error: errors().title}" />
				<small>Tittelen til innholdet</small>
			</p>
			<p>
				<input type="text" placeholder="Når" data-bind="value: time, css: {error: errors().time}") />
				<small>Så langt du vet: yyyy-mm-dd hh:mm:ss</small>
			</p>
			<p>
				<select data-bind="
					value: location_id,
					valueAllowUnset: true,
					options: $root.locations,
					optionsText: 'name',
					optionsValue: 'location_id',
					optionsCaption: 'Hvor',
					attr: {'data-selected': location_id},
					css: {error: errors().location_id}
					"></select>
				<small><a href="javascript:void(0)" tabindex="-1" data-bind="click: $parent.copyLocation.bind($parent, $data)">Kopier til resten</a></small>
			</p>
		</div>

		<p>
			<textarea optional placeholder="Sammendrag" data-bind="value: summary, css: {error: errors().summary}" class="full-width"></textarea>
		</p>
		

		<fieldset data-bind="css: {error: errors().persons || errors().name}">
			<legend>
				Personer
				<a href="javascript:void(0)" data-bind="click: addPerson">{{#svg}}add{{/svg}}</a>
			</legend>
			<!-- ko foreach: persons -->
			<div>
				{{! TODO: Auto-complete person name }}
				<input type="text" placeholder="Fullt navn" data-bind="value: name">

				<select data-bind="
					value: role,
					options: $root.roles,
					optionsText: 'label',
					optionsValue: 'role',
					"></select>
				<a href="javascript:void(0)" tabindex="-1" data-bind="click: $parent.removePerson.bind($parent, $data)">{{#svg}}remove{{/svg}}</a>
			</div>
			<!-- /ko -->
			<div class="field error" data-bind="text: errors().persons"></div>
		</fieldset>

	
		<table class="fixed full-width editor">
			<thead>
				<tr>
					<th>Filer</th>
					<th style="width: 12ch">Type</th>
					<td style="width: 6ch" class="dropzone" data-bind="event: {drop: ondrop}"></td>
				</tr>
			</thead>
			<tbody data-bind="event: {dragstart: ondragstart}">
				<!-- ko foreach: {data: files, afterAdd: doFadeIn} -->
				<tr data-bind="event: {dragstart: ondragstart}">
					<td>
						<input required type="text" title="Filen vil bli lastet ned av brukere med dette navnet" data-bind="textInput: filename">
						<small>
							<b>Filnavn:</b> <span data-bind="text: filename"></span><span data-bind="text: extension"></span>
							<br>
							<b>Hash:</b> <span data-bind="text: hash"></span>
							<br>
							<b>Info:</b> <span data-bind="text: description"></span>
						</small>
					</td>
					<td data-bind="text: type"></td>
					<td class="buttons">
						<span draggable="true">{{#svg}}menu{{/svg}}</span>

						<br>

						<a href="javascript:void(0)" title="Load info from tags in file" tabindex="-1" data-bind="click: $parent.getInfo.bind($parent, $data)">{{#svg}}magnifier{{/svg}}</a>

						<a href="javascript:void(0)" title="Remove the file (does not delete the actual file)" tabindex="-1" data-bind="click: $parent.removeFile.bind($parent, $data)">{{#svg}}remove{{/svg}}</a>
					</td>
				</tr>
				<!-- /ko -->
			</tbody>
			<tfoot data-bind="if: errors().hash || errors().path" class="error">
				<tr>
					<td colspan="3">En eller flere av disse filene er allerede i systemet…</td>
				</tr>
			</tfoot>
		</table>
		<p>
			<button class="icon" data-bind="click: save">{{#svg}}save{{/svg}}</button>
		</p>
	</div>
	
	<!-- /ko -->

</script>

{{/ content}}
{{/ layout }}
