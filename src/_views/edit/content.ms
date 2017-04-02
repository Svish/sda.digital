{{#role.editor}}

<div id="editor" class="gone">
	<div data-bind="with: item">
		
		<p>
			<input type="text" placeholder="Tittel" data-bind="value: title" required>
			<span class="field error" data-bind="text: errors().title"></span>
		</p>
		<p>
			<input type="text" placeholder="Når" data-bind="value: time") />
			<span class="field error" data-bind="text: errors().time"></span>
		</p>
		<p>
			<select data-bind="
				value: location_id,
				valueAllowUnset: true,
				options: $root.locations,
				optionsText: 'name',
				optionsValue: 'location_id',
				optionsCaption: 'Hvor',
				attr: {'data-selected': location_id}
				"></select>
			<span class="field error" data-bind="text: errors().location_id"></span>
		</p>
		<p>
			<textarea optional placeholder="Sammendrag" data-bind="value: summary, css: {error: errors().summary}" class="full-width"></textarea>
			<small>Støtter <a href="http://commonmark.org/help/">Markdown</a>.</small>
			<span class="field error" data-bind="text: errors().summary"></span>
		</p>


		<fieldset>
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
					valueAllowUnset: true,
					options: $root.roles,
					optionsText: 'label',
					optionsValue: 'role',
					"></select>
				<a href="javascript:void(0)" tabindex="-1" data-bind="click: $parent.removePerson.bind($parent, $data)">{{#svg}}remove{{/svg}}</a>
			</div>
			<!-- /ko -->
			<div class="field error" data-bind="text: errors().persons"></div>
		</fieldset>
	</div>

	<p>{{#svg}}error{{/svg}} Fjerner du innholdet, vil også de fysiske filene som er en del av dette innholdet slettes.

	{{> edit/buttons}}
</div>

<script>
var ID = '{{content_id}}';
</script>
<script defer src="js/edit-content.js"></script>

{{/role.editor}}
