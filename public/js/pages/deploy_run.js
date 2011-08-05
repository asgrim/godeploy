var deployment_complete;

function bootstrapDeployment()
{
	var start_url = document.location.href.replace(/\/run\//, '/execute-deployment-start/');
	
	deployment_complete = false;

	fireStatusRequest();
	
	new Ajax.Request(start_url, {
			method: 'get',
			asynchronous: true,
			onSuccess: function(transport){
				var response = transport.responseText;
				deployment_complete = true;
				alert('Deplomyent finished!');
			},
			onFailure: function(){ alert('Something went wrong...') }
		});
}

function fireStatusRequest()
{
	var status_url = document.location.href.replace(/\/run\//, '/execute-deployment-status/');
	new Ajax.Request(status_url, {
		method: 'get',
		asynchronous: true,
		onSuccess: function(transport){
			var response = transport.responseText;
			var data = response.evalJSON();
			
			$('deployment_status').innerHTML = data.OVERALL;
			
			for(var x in data.FILES)
			{
				$('file_' + x + '_status').innerHTML = data.FILES[x];
			}
			$('lol').innerHTML = parseInt($('lol').innerHTML) + 1;
			
			if(!deployment_complete)
			{
				setTimeout('fireStatusRequest()', 1000);
			}
		},
		onFailure: function(){ alert('something went wrong..') }
	});
}