

var API = 'manage/content/api/';


$.getJSON(API+'fresh', function(data)
	{
		var view = new ViewModel(data);
		ko.applyBindings(view);
	});


// Root model
var ViewModel = function(data)
{
	this.dirs = ko.mapping.fromJS(data,
		{
			create: opts => new DirectoryModel(opts.data),
		});

	this.selected = ko.computed(function()
		{
			return this.dirs().reduce(function(list, dir)
			{
				list.push.apply(list, dir.selected());
				return list;
			}, []);
		}, this);

	this.load = (nodes, file) => file.load();

	// TODO: This "tab" stuff in own thing?
	this.steps = ['select-template', 'enter-template'];

	this.current = ko.observable(0);
	this.template = ko.pureComputed(() => this.steps[this.current()]);

	this.canNext = ko.pureComputed(() => this.selected().length > 0 && this.current() < this.steps.length-1);
	this.canPrev = ko.pureComputed(() => this.current() > 0);
	this.next = function(d, e)
	{
		this.current((this.current()+1)%this.steps.length);
		if( ! this.canNext())
			$(e.target).blur();
	};
	this.prev = function(d, e)
	{
		this.current((this.current()-1)%this.steps.length);
		if( ! this.canPrev())
			$(e.target).blur();
	};
}


// Directory with files
var DirectoryModel = function(data)
{
	ko.mapping.fromJS(data, 
		{
			files: {
				create: opts => new FileModel(opts.data),
				},
		}, this);

	this.selectedLabel = ko.pureComputed(() => '( ' + this.selected().length + ' / ' + this.files().length + ' )', this);
	this.selected = ko.computed(function()
	{
		return this.files().filter(f => f.selected());
	}, this);

	this.toggle = function(data, e)
	{
		var allSelected = data.files().every(f => f.selected());
		ko.utils.arrayForEach(data.files(), f => f.selected( ! allSelected));
	};
}


// Group of files
var ContentModel = function(data)
{
	// TODO: How to get this in here?
	// Wrap all files in ContentModel
}

// Single file
var FileModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);
	this.selected = ko.observable(false);

	this.loaded = ko.observable(false);
	this.load = function()
	{
		if( ! this.loaded())
			$.ajax({
				type: 'POST',
				url: API+'file',
				data: ko.toJS(this.path()),
				contentType: 'application/json',
				context: this,
				success: function(data)
				{
					this.loaded(true);
					ko.mapping.fromJS(data, {}, this);
				},
			});
	}
}
