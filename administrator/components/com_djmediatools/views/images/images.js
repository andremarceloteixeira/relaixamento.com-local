
function purgeThumbnails(button) {
	var recAjax = new Request({
	    url: 'index.php?option=com_djmediatools&task=images.purge&tmpl=component&format=raw',
	    method: 'post',
	    encoding: 'utf-8',
	    onSuccess: function(response) {
	    	button.set('text',response);
		}
	});
	recAjax.send();
}

function purgeStylesheets(button) {
	var recAjax = new Request({
	    url: 'index.php?option=com_djmediatools&task=images.purgeCSS&tmpl=component&format=raw',
	    method: 'post',
	    encoding: 'utf-8',
	    onSuccess: function(response) {
	    	button.set('text',response);
		}
	});
	recAjax.send();
}

window.addEvent('domready', function(){
	
	var clearButton = document.id('djmt_delete_images');
	if (clearButton) {
		clearButton.removeAttribute('disabled');
		clearButton.addEvent('click',function(){
			clearButton.setAttribute('disabled', 'disabled');
			purgeThumbnails(clearButton);
		});
	}
	
	var clearButton2 = document.id('djmt_delete_stylesheets');
	if (clearButton2) {
		clearButton2.removeAttribute('disabled');
		clearButton2.addEvent('click',function(){
			clearButton2.setAttribute('disabled', 'disabled');
			purgeStylesheets(clearButton2);
		});
	}
});