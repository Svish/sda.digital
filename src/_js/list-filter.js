



$('#list-filter').on('keyup', function(e)
{
	var items = $('.item-list>*');
	var s = e.target.value;

	items.unmark().show();

	if(s)
		items.mark(s, {
			done: function ()
				{
					items.not(':has(mark)').hide();
				},
			});

	// Goto on enter if single result
	if(s && e.which == 13)
	{
		var item = items.filter(':visible');

		if(item.length != 1)
			return;

		var url = item.find('a').attr('href');
		window.location = Site.Url.Base + url;
	}

});
