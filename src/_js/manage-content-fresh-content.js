
var API = 'manage/content/api/';


$.ajax({
	url: API+'fresh-content',
	method: 'GET',
	data: {path: getQueryParameter('path')},
	success: function(data)
	{
		view = new ViewModel(data);
		ko.applyBindings(view);
	},
});

var ViewModel = function(data)
{
	this.content = ko.mapping.fromJS(data, {create: ø => new ContentModel(ø.data)});

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

			if(source == target)
				return true;

			source.file_list.remove(file);
			target.file_list.push(file);

			if( ! source.file_list().length)
				this.content.remove(source);

			return true;
		};
}



// Group of files
var ContentModel = function(data)
{
	ko.mapping.fromJS(data, 
		{
			file_list: {
				create: ø => new FileModel(ø.data),
				},
		}, this);

	this.selected = ko.observable(false);
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
}



// Single file
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
