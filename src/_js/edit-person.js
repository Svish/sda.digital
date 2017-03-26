
var API = Site.Url.Base+'/editor/api/person';

var ViewModel = function()
{
	this.item = ko.observable(null);
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
				success: data => this.item(ko.mapping.fromJS(data)),
			});
		}

	this.cancel = function()
		{
			this.item(null);
			this.errors({});
			if( !ID )
				window.location = Site.Url.Current+'/../index';
		}
	
	this.save = function()
		{
			$.ajax({
				type: 'PUT',
				url: API,
				data: ko.mapping.toJSON(this.item),
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
			$.ajax({
				type: 'DELETE',
				url: API,
				data: ''+ID,
				contentType: 'application/json',
				success: () => window.location = Site.Url.Current+'/../../index',
			});
		};
}

var view = new ViewModel();
ko.applyBindings(view);
if( ! ID)
	view.edit();
