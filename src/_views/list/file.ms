<table class="file-table full-width editor">
	<thead>
		<tr>
			<th>Fil</th>
			<th>Størrelse</th>
			<th>Lengde</th>
			<th>Kvalitet</th>
			<th style="width: 2ch"></th>
		</tr>
	</thead>
	<tbody class="striped">
		{{#file_list}}
		<tr>
			<td>
				{{#fileIcon}}{{type}}{{/fileIcon}}
				<a href="{{url}}">{{name}}</a>
				<small>
					<b>Sha256:</b> <span class="hash">{{hash | f.hash}}</span>
				</small>

			<td>
				{{tag.size.string}}

			<td>
				{{tag.length.string}}

			<td>
				{{tag.bitrate.string}}
				{{#tag.audio}}
					<br>{{sample_rate.string}}
				{{/tag.audio}}
				{{#tag.video}}
					<br>{{resolution_x}}x{{resolution_y}}
				{{/tag.video}}

			<td>
				<a href="{{url}}/download">{{#svg}}download{{/svg}}</a>
		</tr>
		{{/file_list}}
	</tbody>
</table>
