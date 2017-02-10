// NOTE: http://stackoverflow.com/a/42104455/39321
// CSS animate fade in from left?
// bind a delay to $index * x ms?


$.getJSON('admin/content/api/fresh-files', function(data)
	{
		var view = new ViewModel(data);
		ko.applyBindings(view);
	});


// Root model
var ViewModel = function(data)
{
	this.groups = ko.mapping.fromJS(data,
		{
			create: function(options)
			{
				return new GroupModel(options.data);
			}
		});

	this.totalFiles = ko.computed(function()
	{
		return this.groups().reduce(function(total, group)
		{
			return total + group.files().length;
		}, 0);
	}, this);

	this.selectedFiles = ko.computed(function()
	{
		return this.groups().reduce(function(list, group)
		{
			list.push.apply(list, group.selectedFiles());
			return list;
		}, []);
	}, this)

	this.submitLabel = ko.computed(function()
	{
		return this.selectedFiles().length + ' / ' + this.totalFiles() + ' →';
	}, this);
}


// Group of files
var GroupModel = function(data)
{
	ko.mapping.fromJS(data, 
		{
			files: {
				create: function(options)
				{
					return new FileModel(options.data);
				}
			}
		}, this);

	this.selectedFiles = ko.computed(function()
	{
		return this.files().filter(f => f.selected());
	}, this)

	this.console = window.console;

	this.toggle = function(data, e)
	{
		var allSelected = data.files().every(f => f.selected());
		ko.utils.arrayForEach(data.files(), f => f.selected( ! allSelected));
	}
}


// Single file
var FileModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);

	this.selected = ko.observable(false);
}
