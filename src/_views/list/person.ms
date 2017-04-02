<ul class="item-list person-list no-select">
	{{#person_list}}
	<li class="person" data-id="{{person_id}}">
		<a href="{{url}}">
			<span class="count">{{count}}{{#total}}/{{total}}{{/total}}</span>

			{{#svg}}person{{/svg}}
			{{name}}
			
			{{#role}}<div class="meta">{{role | __ | ucwords}}</div>{{/role}}
		</a>
	</li>
	{{/person_list}}
</ul>
