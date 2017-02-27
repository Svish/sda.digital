
var API = 'manage/content/api/';


$.getJSON(API+'fresh-dirs', function(data)
	{
		var view = new ViewModel(data);
		ko.applyBindings(view);
	});

var ViewModel = function(data)
{
	this.directories = data;
	this.total = data.reduce((t, d) => t + d.count, 0);
}
