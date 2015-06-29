function recreateThumbnails(id) {
	var recAjax = new Request({
	    url: 'index.php?option=com_djcatalog2&task=thumbs.go&tmpl=component&format=raw&image_id=' + id,
	    method: 'post',
	    encoding: 'utf-8',
	    onSuccess: function(response) {
	    	var recProgressBar = document.id('djc_progress_bar');
			var recProgressPercent = document.id('djc_progress_percent');
			
			if (response == 'end') {
				document.id('djc_start_recreation').removeAttribute('disabled');
				//document.id('djc_loader').removeClass('djc_loader_loading');
				recProgressBar.setStyle('width','100%');
				recProgressPercent.innerHTML = '100%';
				return true;
			} else if (response == 'error') {
				alert('Unexpected error');
				document.id('djc_start_recreation').removeAttribute('disabled');
				//document.id('djc_loader').removeClass('djc_loader_loading');
				recProgressBar.setStyle('width','0');
				recProgressPercent.innerHTML = '0%';
			}
			else {
				var jsonObj = JSON.decode(response);

				var percentage = Math.floor(((jsonObj.total - jsonObj.left) / jsonObj.total) * 100);

				recProgressBar.setStyle('width',percentage + '%');
				recProgressPercent.innerHTML = percentage + '%';
				
				return recreateThumbnails(jsonObj.id);
			}
		}
	});
	recAjax.send();
}

function purgeThumbnails() {
	var recAjax = new Request({
	    url: 'index.php?option=com_djcatalog2&task=thumbs.purge&tmpl=component&format=raw',
	    method: 'post',
	    encoding: 'utf-8',
	    onSuccess: function(response) {
	    	alert(response);
	    	window.location.replace(window.location.toString());
		}
	});
	recAjax.send();
}

window.addEvent('domready', function(){
	var recButton = document.id('djc_start_recreation');
	var recProgressBar = document.id('djc_progress_bar');
	var recProgressPercent = document.id('djc_progress_percent');
	
	if (recButton && recProgressBar && recProgressPercent) {
		recButton.removeAttribute('disabled');
		recButton.addEvent('click',function(){
			recButton.setAttribute('disabled', 'disabled');
			//document.id('djc_loader').addClass('djc_loader_loading');
			recreateThumbnails(0);
		});
	}
	
	var clearButton = document.id('djc_start_deleting');
	if (clearButton) {
		clearButton.removeAttribute('disabled');
		clearButton.addEvent('click',function(){
			recButton.setAttribute('disabled', 'disabled');
			purgeThumbnails();
		});
	}
});