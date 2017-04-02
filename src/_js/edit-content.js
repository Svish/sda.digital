var API = Site.Url.Base+'editor/api/content';


var ViewModel = function()
{
	this.item = ko.observable(null);
	this.roles = ko.observable(null);
	this.locations = ko.observable(null);

	this.canEdit = ko.pureComputed(() => !this.item(), this);
	this.canRemove = ko.pureComputed(() => !!ID, this);
	this.canSave = ko.pureComputed(() => !!this.item(), this);
	this.canCancel = ko.pureComputed(() => this.canSave(), this);

	this.edit = function(e)
		{
			$.ajax({
				type: 'GET',
				url: API,
				data: {id: ID},
				context: this,
				success: this.gotit,
			});
		}

	this.gotit = function(data)
	{
		this.item(new ContentModel(data.content));
		this.roles(data.roles);
		this.locations(data.locations);
	}

	this.cancel = function()
		{
			if( ! ID )
			{
				window.location = Site.Url.Current+'/../index';
				return;
			}
			this.item(null);
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
							this.item().errors(x.responseJSON.errors);
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
				success: () => window.location = Site.Url.Base,
			});
		};
}

var ContentModel = function(data)
{
	this.errors = ko.observable({});
	ko.mapping.fromJS(data, 
		{
			persons: {
				create: ø => new PersonModel(ø.data),
				},
		}, this);

	this.addPerson = function()
		{
			var data = {
				person_id: null,
				name: null,
				role: this.persons().length % 2 
					? 'translator'
					: 'speaker',
			};
			this.persons.push(new PersonModel(data));
		}


	this.removePerson = function(person, e)
		{
			this.persons.remove(person);
		};
}


// Person
var PersonModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);
}


var view = new ViewModel();
ko.applyBindings(view);
if( ! ID)
	view.edit();

$('.gone').fadeIn();
