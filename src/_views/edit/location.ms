{{#role.editor}}

<div id="editor" class="gone">
	<div data-bind="with: item">

	<p>{{#svg}}help{{/svg}} Navnefeltet søker automatisk etter en adresse. Velg med musklikk eller enter for å fylle ut adresse og geo-lokasjon automatisk. Prøv piltast-ned dersom ikke noe dukker opp.

	<p>
		<input type="search" placeholder="Navn" data-bind="jqAuto: {
			value: name,
			source: $root.addressLookup,
			labelProp: 'label',
			valueProp: 'value',
			inputProp: 'value',
			dataValue: geo
			}">
		<span class="field error" data-bind="text: errors().name"></span>
	<p>
		<input type="url" placeholder="Nettside" data-bind="value: website">
		<span class="field error" data-bind="text: errors().website"></span>
	<p>

		<textarea placeholder="Adresse" data-bind="value: address"></textarea>
		<span class="field error" data-bind="text: errors().address"></span>
	<p>

		<input type="text"
			class="number"
			title="Latitude"
			placeholder="Lat"
			data-bind="value: latitude">
		<input type="text"
			class="number"
			title="Longitude"
			placeholder="Long"
			data-bind="value: longitude">
		<span class="field error" data-bind="text: errors().latitude"></span>
		<span class="field error" data-bind="text: errors().longitude"></span>


	</div>

	{{> edit/buttons}}
</div>





<script>
var ID = '{{location_id}}';
</script>
<script defer src="https://maps.googleapis.com/maps/api/js?key={{google.api_key}}&language=no"></script>
<script defer src="js/edit-location.js"></script>

{{/role.editor}}
