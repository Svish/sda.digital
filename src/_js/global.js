/**
 * Generic stuff for all pages.
 */



/**
 * Hook up global ajax events.
 * @see https://api.jquery.com/category/ajax/global-ajax-event-handlers/
 */
$(document)
	.ajaxStart(NProgress.start)
	.ajaxStop(NProgress.done)
	.ajaxError(errorHandler);


/**
 * Generic AJAX error handler.
 */
function errorHandler(event, jqxhr, settings, thrownError)
{
	var body = /<body.*>([\s\S]+)<\/body>/
		.exec(jqxhr.responseText);

	if( ! body)
		return;

	var body = $('<output>').append($.parseHTML(body[1]));
	var message = body.find('#message');
	var content = body.find('#content');

	$('#header').after(message);

	if( ! settings.error)
		$('#content').replaceWith(content);
}



/**
 * Generic confirm function.
 */
function doConfirm()
{
	return confirm('Sikker? 🤔');
}
