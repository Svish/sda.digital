
var FileModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);
	this.selected = ko.observable(false);
}

var GroupModel = function(data)
{
	ko.mapping.fromJS(data, {
			'files': {
				create: function(options)
				{
					return new FileModel(options.data);
				}
		}}, this);
}

$(function()
{
	$.getJSON('admin/content/api/fresh-files', function(data)
		{
			view = ko.mapping.fromJS(data, {
					create: function(options)
					{
						return new GroupModel(options.data);
					}
				});
			ko.applyBindings(view);
		});
});

function toggleSection()
{
	var selected = ko.utils.arrayFirst(this.files(), function(file)
	{
		return file.selected();
	});

	ko.utils.arrayForEach(this.files(), function(file)
		{
			file.selected(selected ? false : true);
		})
}

function submitForm()
{
	return $('form')
		.find('input:checkbox')
		.is(':checked');
}

function resetForm()
{
	return confirm('Sikker?');
}
