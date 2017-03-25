/**
 * Generic stuff for all pages.
 */


/**
 * Get GET parameter value
 * @see http://stackoverflow.com/a/5448595/39321
 */
function getQueryParameter(name, clean = true)
{
	var query = window.location.search.substring(1);
	var vars = query.split("&");

	for(var i in vars)
	{
		var pair = vars[i].split("=");
		if(pair[0] == name)
		{
			var value = decodeURIComponent(pair[1]);
			return clean
				? value.replace(/\+/g, ' ')
				: value;
		}
	}

	return false;
}


/**
 * Generic confirmation.
 */
function doConfirm()
{
	return confirm('Sikker? 🤔');
}
function doUnloadConfirm(e)
{
	var msg = '🤔';
	(e || window.event).returnValue = msg;
	return msg;
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
