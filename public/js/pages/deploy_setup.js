
$(document).observe("dom:loaded", function(){
	$('serverId').observe('change', setCurrentRevision);
	setCurrentRevision();
})

function setCurrentRevision()
{
	var serverId = $('serverId').options[$('serverId').selectedIndex].value;
	var url = document.location.href + '/get-last-deployment-revision?server_id=' + serverId;
	url = url.replace(/deploy\/\//, 'deploy/');
	new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport){
				var response = transport.responseText;
				var data = response.evalJSON();				
				$('fromRevision').value = data.fromRevision;
			},
			onFailure: function(){ alert('Something went wrong...') }
		});
}