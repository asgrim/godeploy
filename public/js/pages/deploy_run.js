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

var deployment_complete;

function bootstrapDeployment()
{
	var start_url = document.location.href.replace(/\/run\//, '/execute-deployment-start/').replace(/#/, '');
	
	deployment_complete = false;
	
	new Ajax.Request(start_url, {
			method: 'get',
			asynchronous: true,
			onSuccess: function(transport){
				var response = transport.responseText;
			},
			onFailure: function(){ alert('Something went wrong...') }
		});

	fireStatusRequest();
}

function continueDeployment()
{
	deployment_complete = false;

	fireStatusRequest();
}

function fireStatusRequest()
{
	var status_url = document.location.href.replace(/\/run\//, '/execute-deployment-status/').replace(/#/, '');
	new Ajax.Request(status_url, {
		method: 'get',
		asynchronous: true,
		onSuccess: function(transport){
			var response = transport.responseText;
			var data = response.evalJSON();
			
			// Set overall status
			$('deployment_status').innerHTML = '<img src="/images/icons/' + data.OVERALL_ICON + '" alt="icon"> ' + data.OVERALL;

			// Set status of each file
			if(data.NUM_FILES > 0)
			{
				for(var x in data.FILES)
				{
					$('file_' + x + '_status').innerHTML = '<img src="/images/icons/' + data.FILE_ICONS[x] + '" alt="icon"> ' + data.FILES[x];
				}
			}
			
			// Set that we are complete or not
			if(data.COMPLETE)
			{
				deployment_complete = true;
			}
			
			// If not complete, poll again in a moment
			if(!deployment_complete)
			{
				setTimeout('fireStatusRequest()', 1000);
			}
			else
			{
				document.location.href = document.location.href.replace(/\/run\//, '/result/').replace(/#/, '');
			}
		},
		onFailure: function(){ alert('something went wrong..') }
	});
}