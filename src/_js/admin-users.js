

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
			create: ø => new UserModel(ø.data),
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
			if( ! user.user_id())
				return this.users.remove(user);

			if( ! doConfirm())
				return;

			$.ajax({
				type: 'DELETE',
				url: API+'user',
				data: ''+user.user_id(),
				contentType: 'application/json',
				context: this,
				success: function(data)
				{
					// TODO: Move to UserModel by using _destroy?
					this.users.remove(user);
				},
			});
		};

	this.afterAdd = function(e)
		{
			if(e.nodeType != 1)
				return;

			$(e)
				.find('input')
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
	this.canRemove = ko.pureComputed(() => ! this.editing() || ! this.user_id(), this);
	this.canSave = ko.pureComputed(()=> this.editing(), this);
	this.canCancel = ko.pureComputed(() => this.editing() && this.user_id(), this);

	this.cancel = Model.cancel;
	this.keydown = Model.inputKeydown;

	this.errors = ko.observable({});

	this.save = function()
		{
			$.ajax({
				type: 'PUT',
				url: API+'user',
				data: ko.mapping.toJSON(this),
				contentType: 'application/json',
				context: this,
				success: Model.save.success,
				error: Model.save.error,
			});
		};
}
