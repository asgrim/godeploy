
$(document).observe("dom:loaded", function(){
	$('serverId').observe('change', setCurrentRevision);
	setCurrentRevision();
});

var fetching_latest_revision = false;

function setCurrentRevision()
{
	var serverId = $('serverId').options[$('serverId').selectedIndex].value;
	var url = document.location.href + '/get-last-deployment-revision?server_id=' + serverId;
	url = url.replace(/deploy\/\//, 'deploy/').replace(/#/, '');
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

function getLatestRevision()
{
	var parent_wrapper = $("get_latest_revision_status").up(".wrapper");
	var wrapper_colour = parent_wrapper.className.match(/w_(\w{6})/);
	var loading_img = '<img src="/images/icons/running/on_' + wrapper_colour[1] + '/16x16.gif" alt="Loading..." class="loading" />';

	if(fetching_latest_revision)
	{
		$('get_latest_revision_status').innerHTML = loading_img + ' Please wait, it can take a few seconds!';
		return;
	}
	
	$('get_latest_revision_status').innerHTML = loading_img + ' Please wait, fetching latest revision...';
	
	fetching_latest_revision = true;
	
	var url = document.location.href + '/get-latest-revision';
	url = url.replace(/deploy\/\//, 'deploy/').replace(/#/, '');
	new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport){
				var response = transport.responseText;
				var data = response.evalJSON();				
				$('toRevision').value = data.toRevision;
				
				$('get_latest_revision_status').innerHTML = '';
				fetching_latest_revision = false;
			},
			onFailure: function(){ alert('Something went wrong...') }
		});
}