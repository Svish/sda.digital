<ul class="item-list location-list no-select">
	{{#location_list}}
	<li class="location" data-id="{{location_id}}">
		<a href="{{url}}">
			<span class="count">{{count}}</span>
			{{#svg}}place{{/svg}}
			{{name}}
		</a>
	</li>
	{{/location_list}}
</ul>
