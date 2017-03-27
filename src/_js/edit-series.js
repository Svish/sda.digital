var API = Site.Url.Base+'/editor/api/series';


var ViewModel = function()
{
	this.item = ko.observable(null);
	this.fresh = ko.observable(null);

	this.errors = ko.observable({});

	this.canEdit = ko.pureComputed(() => !this.item(), this);
	this.canRemove = ko.pureComputed(() => !!ID, this);
	this.canSave = ko.pureComputed(() => !!this.item(), this);
	this.canCancel = ko.pureComputed(() => this.canSave(), this);

	this.edit = function(e)
		{
			$.ajax({
				url: API,
				data: {id: ID},
				context: this,
				success: this.gotit,
			});
		}


	this.originalList = null;
	this.gotit = function(data)
	{
		this.item(ko.mapping.fromJS(data.series));
		this.fresh(data.content);

		this.originalList = $(".content-list").html();
		$(".content-list:first").sortable({
			placeholder: 'ui-sortable-placeholder',
			axis: 'y',
			forcePlaceholderSize: true,
		});
	}


	this.cancel = function()
		{
			if( ! ID )
			{
				window.location = Site.Url.Current+'/../index';
				return;
			}
			this.item(null);
			this.fresh(null);
			this.errors({});

			$(".content-list:first")
				.html(this.originalList)
				.sortable('refresh')
				.sortable('destroy');
		}
	
	this.save = function()
		{
			var data = {
				series: ko.mapping.toJS(this.item),
				content: $('.content-list')
					.sortable('toArray', {attribute: 'data-id'}),
			};
			$.ajax({
				type: 'PUT',
				url: API,
				data: JSON.stringify(data),
				contentType: 'application/json',
				context: this,
				success: function(x)
					{
						 if(x.url)
						 	window.location = Site.Url.Base+x.url;
					},
				error: function(x)
					{
						if(x.responseJSON && x.responseJSON.errors)
							this.errors(x.responseJSON.errors);
					},
			});
		};


	this.remove = function(person, e)
		{
			if( ! doConfirm())
				return;
				
			$.ajax({
				type: 'DELETE',
				url: API,
				data: ''+ID,
				contentType: 'application/json',
				success: () => window.location = Site.Url.Current+'/../../index',
			});
		};

	this.addItem = function(e)
	{
		$(e.currentTarget)
			.closest('li')
			.appendTo('.ui-sortable:first');
		$('.ui-sortable:first')
			.sortable('refresh');
	}

	this.removeItem = function(e)
	{
		$(e.currentTarget)
			.closest('li')
			.prependTo('section.fresh .content-list');
		$('.ui-sortable:first')
			.sortable('refresh');
	}

	this.upItem = function(e)
	{
		var x = $(e.currentTarget).closest('li');
		x.prev().insertAfter(x);
		$('.ui-sortable:first')
			.sortable('refresh');
	}

	this.downItem = function(e)
	{
		var x = $(e.currentTarget).closest('li');
		x.next().insertBefore(x);
		$('.ui-sortable:first')
			.sortable('refresh');
	}
}


var view = new ViewModel();
ko.applyBindings(view);
if( ! ID)
	view.edit();


$('.fresh')
	.on('click', 'a', false)
	.on('click', 'button.add', view.addItem);
	
$('.content-list')
	.on('click', 'button.up', view.upItem)
	.on('click', 'button.down', view.downItem)
	.on('click', 'button.remove', view.removeItem)
	.on('click', 'a', function(e)
	{
		return ! $(e.currentTarget)
			.parent()
		 	.hasClass('ui-sortable-handle');
	});

$('.gone').fadeIn();
