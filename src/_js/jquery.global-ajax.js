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
	$('.waiting').remove();
}

function onAjaxErrorHandler(event, x, settings, thrownError)
{
	if( ! x.responseJSON)
		return;

	$('#header')
		.after(x.responseJSON.message);
	
	if(x.status >= 500)
		$('#content')
			// TODO: .html()
			.append(x.responseJSON.reason);

}
