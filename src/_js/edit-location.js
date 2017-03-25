
var API = Site.Url.Base+'/editor/api/location';

var ViewModel = function()
{
	this.item = ko.observable(null);

	this.canEdit = ko.pureComputed(() => !this.item(), this);
	this.canRemove = ko.pureComputed(() => !!ID, this);
	this.canSave = ko.pureComputed(() => !!this.item(), this);
	this.canCancel = ko.pureComputed(() => this.canSave(), this);

	this.edit = function(e)
		{
			$.ajax({
				url: API,
				data: {id: ID},
				context: this,
				success: data => this.item(new LocationModel(data)),
			});
		}

	this.cancel = function()
		{
			this.item(null);
			this.errors({});
		}
	
	this.save = function()
		{
			$.ajax({
				type: 'PUT',
				url: API,
				data: ko.mapping.toJSON(this.item),
				contentType: 'application/json',
				context: this,
				success: function(x)
					{
						 if(x.url)
						 	window.location = Site.Url.Base+x.url;
					},
				error: function(x)
					{
						if(x.responseJSON && x.responseJSON.errors)
							this.item().errors(x.responseJSON.errors);
					},
			});
		};


	this.remove = function(person, e)
		{
			$.ajax({
				type: 'DELETE',
				url: API,
				data: ''+ID,
				contentType: 'application/json',
				success: () => window.location = Site.Url.Current+'/../../index',
			});
		};


	this.geocoder = new google.maps.Geocoder();
	this.addressLookup = function(request, response)
	{
		var data = {
			address:request,
			language: 'no',
			region: 'no',
			};
		view.geocoder.geocode(data, function(results, status)
		{
			if (status == google.maps.GeocoderStatus.OK)
			{
				var res = $.map(results, function(ø)
				{
					return {
						label: ø.formatted_address,
						value: request,
						geo: ø,
					};
				});
				console.info(res);
				response(res);
			}
			else
			{
				response([]);
			}
		});
	}
}



var LocationModel = function(data)
{
	ko.mapping.fromJS(data, {}, this);

	this.errors = ko.observable({});
	this.geo = ko.pureComputed({
		owner: this,
		read: () => null,
		write: function(ø)
			{
				if( ! ø) return;
				this.address(ø.geo.formatted_address.replace(/,\s+/gm, "\n"));
				this.latitude(ø.geo.geometry.location.lat().toFixed(6));
				this.longitude(ø.geo.geometry.location.lng().toFixed(6));
			},

	});
}


var view = new ViewModel();
ko.applyBindings(view);
if( ! ID)
	view.edit();
