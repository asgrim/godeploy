/**
 * GoDeploy deployment application
 * Copyright (C) 2011 the authors listed in AUTHORS file
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright 2011 GoDeploy
 * @author See AUTHORS file
 * @link http://www.godeploy.com/
 */

$(document).observe("dom:loaded", function(){
	$('serverId').observe('change', setCurrentRevision);
	setCurrentRevision();
});

var fetching_latest_revision = false;

function setCurrentRevision()
{
	var serverId = $('serverId').options[$('serverId').selectedIndex].value;
	var url = location.protocol + '//' + location.host + location.pathname + '/get-last-deployment-revision?server_id=' + serverId;
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
	
	var url = location.protocol + '//' + location.host + location.pathname + '/get-latest-revision';
	url = url.replace(/deploy\/\//, 'deploy/').replace(/#/, '');
	url = url + '?from_revision=' + $('fromRevision').value;
	new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport){
				var response = transport.responseText;
				var data = response.evalJSON();				
				$('toRevision').value = data.toRevision;
				
				if($('comment').value == '')
				{
					if($('fromRevision').value == '')
					{
						$('comment').value = 'Initial deployment';
					}
					else
					{
						$('comment').value = data.autoComment;
					}
				}
				
				getCommitList();

				$('get_latest_revision_status').innerHTML = '';
				fetching_latest_revision = false;
			},
			onFailure: function(){ alert('Something went wrong...') }
		});
}

function getCommitList()
{
	if ($('toRevision').value == '')
	{
		alert('You must specify a Deploy revision or tag first!');
		return;
	}

	var url = location.protocol + '//' + location.host + location.pathname + '/get-commit-list';
	url = url.replace(/deploy\/\//, 'deploy/').replace(/#/, '');
	url = url + '?from_revision=' + $('fromRevision').value + '&to_revision=' + $('toRevision').value;
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport){
			var response = transport.responseText;
			var data = response.evalJSON();				

			var table = document.createElement('TABLE');
			table.setAttribute('style', 'width: auto;');

			var thead = document.createElement('THEAD');

			var head_tr = document.createElement('TR');

			var head_tr_th1 = document.createElement('TH');
			head_tr_th1.innerText = 'Hash';
			head_tr.appendChild(head_tr_th1);

			var head_tr_th2 = document.createElement('TH');
			head_tr_th2.innerText = 'Author';
			head_tr.appendChild(head_tr_th2);

			var head_tr_th3 = document.createElement('TH');
			head_tr_th3.innerText = 'Message';
			head_tr.appendChild(head_tr_th3);

			thead.appendChild(head_tr);
			table.appendChild(thead);

			tbody = document.createElement('TBODY');

			data.commits.each(function (commit) {
				var row_tr = document.createElement('TR');

				var commitHashSpan = document.createElement('SPAN');
				commitHashSpan.setAttribute('class', 'monospace')
				commitHashSpan.innerText = commit.HASH.substring(0, 7);

				var row_tr_td1 = document.createElement('TD');
				row_tr_td1.appendChild(commitHashSpan);
				row_tr.appendChild(row_tr_td1);

				var row_tr_td2 = document.createElement('TD');
				row_tr_td2.innerText = commit.AUTHOR;
				row_tr.appendChild(row_tr_td2);

				var row_tr_td3 = document.createElement('TD');
				row_tr_td3.innerText = commit.MESSAGE;
				row_tr.appendChild(row_tr_td3);

				tbody.appendChild(row_tr);
			});

			table.appendChild(tbody);

			while ($('pre_commit_list').firstChild)
			{
				$('pre_commit_list').removeChild($('pre_commit_list').firstChild);
			}

			$('pre_commit_list').appendChild(table);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}
