/**
 * Knockout.js things
 *
 * - http://www.knockmeout.net/2011/04/utility-functions-in-knockoutjs.html
 * - http://knockoutjs.com/documentation/custom-bindings.html
 * - http://knockoutjs.com/documentation/plugins-mapping.html
 */



Model = {
	cancel: function()
	{
			this.editing(false);
			ko.mapping.fromJS(this.original, {}, this);
	},
	save: {
		success: function(data)
			{
				this.editing(false);
				this.errors({});
				ko.mapping.fromJS(data, {}, this);
			},
		error: function(jqxhr, status, error)
			{
				if(jqxhr.responseJSON.errors)
					this.errors(jqxhr.responseJSON.errors);
			},
	},
};


/**
 * Filtered observable array
 * @see http://stackoverflow.com/a/13216571/39321
 */
 ko.observableArray.fn.filtered = function(key)
 {
	return ko.computed({
		read: function() { return this()[key]; },
	}, this);
 }



/**
 * Faded visible
 * @see http://knockoutjs.com/examples/animatedTransitions.html
 */
ko.bindingHandlers.fadeVisible =
{
	init: function(element, valueAccessor)
	{
		var value = valueAccessor();
		$(element).toggle(ko.unwrap(value));
	},
	update: function(element, valueAccessor)
	{
		var value = valueAccessor();
		ko.unwrap(value) ? $(element).fadeIn(250) : $(element).fadeOut(250);
	}
};



/**
 * Lazy observable
 * @see http://www.knockmeout.net/2011/06/lazy-loading-observable-in-knockoutjs.html
 */
ko.lazyObservable = function(callback, target)
{
	var _value = ko.observable(); 
	
	var result = ko.computed({
			deferEvaluation: true,
			read: function()
				{
					if( ! result.loaded())
						callback.call(target);
					return _value();
				},
			write: function(newValue)
			{
				result.loaded(true);
				_value(newValue);
			},
		});

	result.loaded = ko.observable();
	result.refresh = () => result.loaded(false);
	return result;
};



/**
 * Knockout: Dirty flag
 * @see http://www.knockmeout.net/2011/05/creating-smart-dirty-flag-in-knockoutjs.html
 */
ko.dirtyFlag = function(root, isInitiallyDirty) {
	var result = function() {},
		_initialState = ko.observable(ko.toJSON(root)),
		_isInitiallyDirty = ko.observable(isInitiallyDirty);

	result.isDirty = ko.computed(function() {
		return _isInitiallyDirty() || _initialState() !== ko.toJSON(root);
	});

	result.reset = function() {
		_initialState(ko.toJSON(root));
		_isInitiallyDirty(false);
	};

	return result;
};


/**
 * Knockout: ContentEditable binding.
 * @see http://stackoverflow.com/a/19378038/39321
 */
ko.bindingHandlers.editableValue = {

	update: function (element, valueAccessor)
	{
		var value = ko.unwrap(valueAccessor());
		
		if ( ! element.isContentEditable)
		{
			element.innerHTML = value;
		}
	}
};
ko.bindingHandlers.editable = {

	init: function (element, valueAccessor, allBindings, view, context)
	{
		var value = ko.unwrap(valueAccessor());
		var editableValue = allBindings().editableValue;
		
		$(element).on('keydown', function(e)
		{
			switch(e.which)
			{
				case 13:
					context.$data.save();
					return false;
				 case 27:
					context.$data.cancel();
					return false;
			}
		});
		$(element).on('input', function()
		{
			if(this.isContentEditable && ko.isWriteableObservable(editableValue))
			{
				var text = $(this).text().superTrim() || null;
				editableValue(text);
			}
		});

		$(element).on('blur', function()
		{
			if(this.isContentEditable)
			{
				this.innerHTML = editableValue();
			}
		});

	},

	update: function (element, valueAccessor)
	{
		var value = ko.unwrap(valueAccessor());

		element.contentEditable = value;

		if ( ! element.isContentEditable)
		{
			$(element).trigger("input");
		}
	}
};
