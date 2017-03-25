

var API = Site.Url.Current+'/api/';


$.getJSON(API+'list', function(data)
	{
		view = new ViewModel(data);
		ko.applyBindings(view);
	});




// Root model
var ViewModel = function(data)
{
	this.list = ko.mapping.fromJS(data,
		{
			create: ø => new LocationModel(ø.data),
		});

	this.add = function()
		{
			$.ajax({
				url: API+'new',
				context: this,
				success: function(data)
				{
					var location = new LocationModel(data);
					location.editing(true);
					this.list.push(location);
				},
			});
		};

	this.remove = function(location, e)
		{
			if( ! location.location_id())
				return this.list.remove(location);

			if( ! doConfirm())
				return;

			$.ajax({
				type: 'DELETE',
				url: API+'location',
				data: ''+location.location_id(),
				contentType: 'application/json',
				context: this,
				success: function(data)
				{
					this.list.remove(location);
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
	ko.mapping.fromJS(data, {
		ignore: ['content_list', 'name_slug'],
	}, this);

	this.websiteText = ko.pureComputed(() => (this.website() || '').replace(/.+:\/\//, ''));

	this.original = data;
	this.editing = ko.observable(false);
	this.edit = location => location.editing(true);

	this.canEdit = ko.pureComputed(() => ! this.editing(), this);
	this.canRemove = ko.pureComputed(() => ! this.editing() || ! this.location_id(), this);
	this.canSave = ko.pureComputed(()=> this.editing(), this);
	this.canCancel = ko.pureComputed(() => this.editing() && this.location_id(), this);

	this.cancel = Model.cancel;

	this.errors = ko.observable({});

	this.save = function()
		{
			$.ajax({
				type: 'PUT',
				url: API+'location',
				data: ko.mapping.toJSON(this),
				contentType: 'application/json',
				context: this,
				success: Model.save.success,
				error: Model.save.error,
			});
		};

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
