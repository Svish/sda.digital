{{% ACCESS admin }}
{{% BLOCKS }}
{{< layout }}
{{$ title }}Brukere{{/ title }}

{{$ content}}

<h1>Brukere</h1>

<table id="user-table" class="fixed full-width editor">
	<thead>
		<tr>
			<th style="width: 32%">Name</th>
			<th style="width: 32%">Email</th>
			<th style="width: 32%">Roles</th>
			<th style="width: 6ch"></th>
		</tr>
	</thead>
	<tbody>
		{{> waiting-row }}
		<!-- ko template: {name: 'user-row', foreach: users, afterAdd: afterAdd} -->
		<!-- /ko -->
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">
			<td>
				<a href="javascript:void(0)" data-bind="click: add">{{#svg}}add{{/svg}}</a>
			</td>
		</td>
	</tfoot>
</table>


<script type="text/html" id="user-row">
	<tr spellcheck="false">
		<!-- ko ifnot: editing -->
		<td data-bind="text: name, attr: {title: name}"></td>
		<td data-bind="text: email, attr: {title: email}"></td>
		<td data-bind="text: roles, attr: {title: roles}"></td>
		<!-- /ko -->
	
		<!-- ko if: editing -->
		<td>
			<input required type="text" data-bind="value: name">
			<div class="cell error" data-bind="text: errors().name"></div>
		</td>
		<td>
			<input required type="email" data-bind="value: email">
			<div class="cell error" data-bind="text: errors().email"></div>
		</td>
		<td>
			<input type="text" data-bind="value: roles">
			{{! TODO: http://knockoutjs.com/documentation/selectedOptions-binding.html }}
			<div class="cell error" data-bind="text: errors().roles"></div>
		</td>
		<!-- /ko -->

		<td class="buttons">
			<a href="javascript:void(0)" data-bind="visible: canEdit, click: edit">{{#svg}}edit{{/svg}}</a>
			
			<a href="javascript:void(0)" data-bind="visible: canRemove, click: $parent.remove.bind($parent, $data)">{{#svg}}remove{{/svg}}</a>

			<a href="javascript:void(0)" data-bind="visible: canCancel, click: cancel">{{#svg}}close{{/svg}}</a>

			<a href="javascript:void(0)" data-bind="visible: canSave, click: save">{{#svg}}save{{/svg}}</a>
		</td>
	</tr>
</script>


<script defer src="js/admin-users.js"></script>


{{/ content}}
{{/ layout }}
