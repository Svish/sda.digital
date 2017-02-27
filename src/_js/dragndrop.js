/**
 * @see http://mereskin.github.io/dnd/
 * @see https://mdn.github.io/dom-examples/drag-and-drop/copy-move-DataTransfer.html
 */


document.addEventListener("drag", function(e)
{
	e.preventDefault();
	// TODO: Scroll on top/bottom
	// https://codepen.io/JTParrett/pen/rkofB
	// Add/Remove event on dragstart and dragend
}, false);


document.addEventListener("dragstart", function(e)
{
	if( ! e.target.draggable)
	{
		e.stopImmediatePropagation();
		return;
	}

	e.dataTransfer.setData('text/html', e.target.outerHTML.trim());
	e.dataTransfer.setData('text/plain', e.target.textContent.trim());
}, false);


document.addEventListener("dragend", function(e)
{
	if( ! e.target.draggable)
	{
		e.stopImmediatePropagation();
		return;
	}
}, false);


document.addEventListener("dragover", function(e)
{
	if(e.target.classList.contains('dropzone'))
	{
		e.preventDefault();
		e.target.classList.add('accept');
	}
}, false);


document.addEventListener("dragenter", function(e)
{
	// TODO: Find parent with .dropzone
	if(e.target.classList && e.target.classList.contains('dropzone'))
	{
		e.preventDefault();
		e.target.classList.add('accept');
	}
}, false);


document.addEventListener("dragleave", function(e)
{
	// TODO: Find parent with .dropzone
	if(e.target.classList && e.target.classList.contains('dropzone'))
	{
		e.preventDefault();
		e.target.classList.remove('accept');
	}
}, false);


document.addEventListener("dragexit", function(e)
{
	
}, false);


document.addEventListener("drop", function(e)
{
	e.preventDefault();	
	if(e.target.classList.contains('dropzone'))
	{
		e.target.classList.remove('accept');
	}
	
}, false);
