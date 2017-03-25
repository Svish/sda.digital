

var API = 'manage/fresh/api/';


$.getJSON(API+'fresh-content-for-series', 
	{path: getQueryParameter('path')},
	function(data)
	{
		view = new ViewModel(data);
		ko.applyBindings(view);
	}
);



var ViewModel = function(data)
{
	this.content = ko.mapping.fromJS(data, {create: ø => new ContentModel(ø.data)});
	this.series = ko.observable(null);

	this.selected = ko.computed(function()
	{
		return this.content().filter(f => f.selected());
	}, this);

	this.toggle = function(data, e)
	{
		var allSelected = data.content().every(ø => ø.selected());
		ko.utils.arrayForEach(data.content(), ø => ø.selected( ! allSelected));
	};


	this.seriesLookup = function(request, response)
	{
		$.getJSON(API+'lookup-series', {term: request}, response);
	}
}


var ContentModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);

	this.selected = ko.observable(false);
}


var SeriesModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);
}
