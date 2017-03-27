
var API = Site.Url.Base+'manage/fresh/api/';


$.ajax({
	type: 'GET',
	url: API+'fresh-content',
	data: {path: getQueryParameter('path')},
	success: function(data)
	{
		view = new ViewModel(data);
		ko.applyBindings(view);

		if(Site.Env != 'dev')
			window.addEventListener('beforeunload', doUnloadConfirm);
	},
});



var ViewModel = function(data)
{
	this.content = ko.mapping.fromJS(data.content, {create: ø => new ContentModel(ø.data)});
	this.locations = data.locations;
	this.roles = data.roles;
	this.path = data.path;

	this.ondragstart = function(data, e)
		{
			this.dragging = [
				e.originalEvent.fromContent,
				e.originalEvent.draggedFile];
			return true;
		};

	this.ondrop = function(data, e)
		{
			var file = this.dragging[1];
			var source = this.dragging[0];
			var target = e.originalEvent.toContent;

			this.dragging = null;

			if(source == target)
				return true;

			source.files.remove(file);
			target.files.push(file);

			if( ! source.files().length)
				this.content.remove(source);

			return true;
		};

	this.copyLocation = function(data)
	{
		this.content().forEach(ø => ø.location_id(data.location_id()))
	}

	this.beforeRemove = function(e)
	{
		$(e).fadeOut(() => $(this).remove());
	}
}



// Content: Group of files
var ContentModel = function(data)
{
	ko.mapping.fromJS(data, 
		{
			files: {
				create: ø => new FileModel(ø.data),
				},
		}, this);

	this.ondragstart = function(data, e)
		{
			e.originalEvent.fromContent = data;
			return true;
		};
	this.ondrop = function(data, e)
		{
			e.originalEvent.toContent = data;
			return true;
		};

	this.doFadeIn = function(tag)
	{
		if(tag.nodeType != 1)
			return;
		$(tag)
			.hide()
			.fadeIn(500);
	}

	this.removeFile = function(file, e)
		{
			if(doConfirm())
				return this.files.remove(file);
		};

	this.getInfo = function(file, e)
		{
			$.ajax({
				type: 'GET',
				url: API+'tag-info',
				data: {path: file.path()},
				contentType: 'application/json',
				context: this,
				success: function(data)
				{
					ko.mapping.fromJS(data, {}, this);
				},
			});
		}

	this.addPerson = function()
		{
			var data = {
				person: {person_id: null, name: null},
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

	this.errors = ko.observable({});
	this.save = function()
		{
			if( ! doConfirm())
				return;

			$.ajax({
				type: 'PUT',
				url: API+'content',
				data: ko.mapping.toJSON(this),
				contentType: 'application/json',
				context: this,
				success: function(data)
					{
						view.content.remove(this);
					},
				error: x => {
					if(x.responseJSON && x.responseJSON.errors)
						this.errors(x.responseJSON.errors);
					},
			});
		};
}


// Person
var PersonModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);
}


// File: Single, actual file
var FileModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);

	this.ondragstart = function(data, e)
		{
			e.originalEvent.dataTransfer.effectAllowed = 'move';
			e.originalEvent.draggedFile = data;
			return true;
		};
}


document.addEventListener("dragstart", function(e)
{
}, false);
