
var FileModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);
}

var ContentModel = function(data)
{
	ko.mapping.fromJS(data, {
			'files': {
				create: function(options)
				{
					return new FileModel(options.data);
				}
		}}, this);

}

$.getJSON('admin/content/api/selected-files', function(data)
	{
		view = ko.mapping.fromJS(data, {
				create: function(options)
				{
					return new ContentModel(options.data);
				}
			});
		ko.applyBindings(view);
	});
