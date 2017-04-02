<ul class="item-list content-list no-select">
	{{#content_list}}
	<li data-id="{{content_id}}" class="{{#fresh}}fresh{{/fresh}}">
		<a href="{{url}}">

			{{#role.editor}}
			<button class="down">{{#svg}}down-arrow{{/svg}}</button>
			<button class="up">{{#svg}}up-arrow{{/svg}}</button>
			<button class="add">{{#svg}}add{{/svg}}</button>
			<button class="remove">{{#svg}}remove{{/svg}}</button>
			{{/role.editor}}
		
			
			{{#n}}<span class="count">#{{n}}</span>{{/n}}

			<div class="icons">{{#fileIcon}}{{types}}{{/fileIcon}}</div>
			
			{{title}}

			<div class="meta">
				{{#time}}<time datetime="{{time}}">{{time | f.flex}}</time>{{/time}}
				{{#persons}}<div class="persons">{{persons}}</div>{{/persons}}
				{{#roles}}<div class="roles">{{roles | __ | ucwords}}</div>{{/roles}}
				{{#location}}<div class="location">{{location}}</div>{{/location}}
			</div>

		</a>
	</li>
	{{/content_list}}
</ul>
