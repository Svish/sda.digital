/**
 * Generic stuff for all pages.
 */


/**
 * Generic confirmation functions.
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
