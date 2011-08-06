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
				alert(response);
				/*var data = response.evalJSON();
				
				if(data.started)
				{
					alert(response);
				}
				else
				{
					alert('An error happened - couldn\'t start deployment...');
				}*/
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
	
			$('lol').innerHTML = parseInt($('lol').innerHTML) + 1;
			
			// Set overall status
			$('deployment_status').innerHTML = data.OVERALL;
			
			// Set status of each file
			for(var x in data.FILES)
			{
				$('file_' + x + '_status').innerHTML = data.FILES[x];
			}
			
			// Set that we are complete or not
			if(data.COMPLETE)
			{
				alert("Deployment finished.");
				deployment_complete = true;
			}
			
			// If not complete, poll again in a moment
			if(!deployment_complete)
			{
				setTimeout('fireStatusRequest()', 1000);
			}
		},
		onFailure: function(){ alert('something went wrong..') }
	});
}