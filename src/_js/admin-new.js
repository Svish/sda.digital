
$(function()
{

	$('button:not(:submit)').click(function()
	{
		var foo = "go";
		var boxes = $(this)
			.closest('form')
			.find('input:checkbox');
		boxes.prop('checked', boxes.is(':not(:checked)'));
		return false;
	});

	$('button:submit').click(function()
	{
		return $(this)
			.closest('form')
			.find('input:checkbox')
			.is(':checked');
	});
});
