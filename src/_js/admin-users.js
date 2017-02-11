

var API = Site.Url.Current+'/api/';


$.getJSON(API+'users', function(data)
	{
		var view = new ViewModel(data);
		ko.applyBindings(view);
	});


// Root model
var ViewModel = function(data)
{
	this.users = ko.mapping.fromJS(data,
		{
			create: opts => new UserModel(opts.data),
		});

	this.add = function()
	{
		$.ajax({
			url: API+'new',
			context: this,
			success: function(data)
			{
				var user = new UserModel(data);
				user.editing(true);
				this.users.push(user);
			},
		});
	};

	this.remove = function(user)
	{
		if( ! user.id())
			return this.users.remove(user);

		if( ! doConfirm())
			return;

		$.ajax({
			type: 'DELETE',
			url: API+'user',
			data: ''+user.id(),
			contentType: 'application/json',
			context: this,
			success: function(data)
			{
				this.users.remove(user);
			},
		});
	};

	this.afterAdd = function(e)
	{
		if(e.nodeType != 1)
			return;

		$(e)
			.find('[contentEditable=true]')
			.first()
			.focus();
	};
}

// Single file
var UserModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);

	this.original = data;
	this.editing = ko.observable(false);
	this.edit = user => user.editing(true);

	this.canEdit = ko.pureComputed(() => ! this.editing(), this);
	this.canRemove = ko.pureComputed(() => ! this.editing() || ! this.id(), this);
	this.canSave = ko.pureComputed(()=> this.editing(), this);
	this.canCancel = ko.pureComputed(() => this.editing() && this.id(), this);

	this.cancel = function()
	{
		this.editing(false);
		ko.mapping.fromJS(this.original, {}, this);
	};

	this.errors = ko.observableArray();

	this.save = function()
	{
		$.ajax({
			type: 'PUT',
			url: API+'user',
			data: ko.mapping.toJSON(this),
			contentType: 'application/json',
			context: this,
			success: function(data)
			{
				this.editing(false);
				ko.mapping.fromJS(data, {}, this);
			},
			error: function(jqxhr, status, error)
			{
				if(jqxhr.responseJSON.errors)
				{
					// TODO: Hmm...
					this.errors(jqxhr.responseJSON.errors);
				}
			},
		});
	};
}
