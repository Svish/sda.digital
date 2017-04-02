<ul class="item-list series-list no-select">
	{{#series_list}}
	<li class="series" data-id="{{series_id}}">
		<a href="{{url}}">
			{{^n}}<span class="count">{{count}}{{#total}}/{{total}}{{/total}}</span>{{/n}}
			{{#n}}<span class="count">#{{n}}</span>{{/n}}

			{{#svg}}list{{/svg}}
			{{title}}			

			{{#persons}}
			<div class="meta">
				{{persons}}
			</div>
			{{/persons}}

		</a>
	</li>
	{{/series_list}}
</ul>
