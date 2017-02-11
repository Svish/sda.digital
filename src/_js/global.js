/**
 * Generic stuff for all pages.
 */


/**
 * Generic confirm function.
 */
function doConfirm()
{
	return confirm('Sikker? 🤔');
}


/**
 * Hook up global ajax events.
 * @see https://api.jquery.com/category/ajax/global-ajax-event-handlers/
 */
$(document)
	.ajaxStart(onAjaxStartHandler)
	.ajaxSend(onAjaxSendHandler)
	.ajaxStop(onAjaxStopHandler)
	.ajaxError(onAjaxErrorHandler);

function onAjaxStartHandler()
{
	NProgress.start();
}

function onAjaxSendHandler(e, x, opts)
{
	x.setRequestHeader('Is-Ajax', true);
}

function onAjaxStopHandler()
{
	NProgress.done();
	$('.waiting')
		.slideUp(1000); //, () => $(this).remove());
}

function onAjaxErrorHandler(event, jqxhr, settings, thrownError)
{
	var m = jqxhr.responseJSON.message;

	if( ! m)
		return;

	$('#header')
		.after(m);
}
