
$(function()
{
	// TODO: Catch links loaded via ajax too
	$('a[href*="://"]')
		.attr('target', '_blank');

});
