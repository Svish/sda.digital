

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
	this.original = data;

	ko.mapping.fromJS(data, {}, this);

	this.editing = ko.observable(false);

	this.canEdit = ko.computed(() => ! this.editing(), this);
	this.canRemove = ko.computed(() => ! this.editing() || ! this.id(), this);
	this.canSave = ko.computed(()=> this.editing(), this);
	this.canCancel = ko.computed(() => this.editing() && this.id(), this);

	this.edit = user => user.editing(true);

	this.cancel = function()
	{
		this.editing(false);
		ko.mapping.fromJS(this.original, {}, this);
	};

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
		});
	};
}
