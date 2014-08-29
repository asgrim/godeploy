
var deployId = null;

function setDeployStatus(status)
{
	$('#deploy-status').html(status);
	setAvailableActions();
}
function setAvailableActions()
{
	switch ($('#deploy-status').html())
	{
		case 'PREVIEW':
			$('#btn-deploy').prop('disabled', false);
			$('#btn-deploy').html('Deploy');
			break;
		case 'RUNNING':
			$('#btn-deploy').prop('disabled', true);
			$('#btn-deploy').html('Please wait, deploying ...');
			break;
		default:
			$('#btn-deploy').prop('disabled', true);
			$('#btn-deploy').html('Deploy');
			break;
	}
}

$(function () {
	deployId = $('#deploy-id').html();
	setAvailableActions();
	$('#btn-deploy').click(function (e) {
		setDeployStatus('RUNNING');
		$.ajax({
			url: '/run-deployment/' + deployId,
			dataType: 'json'
		})
			.done(function (result) {
				setDeployStatus(result.deployment.status);
				$('#deploy-result-content').html(result.textContent);
			})
			.fail(function () {
				setDeployStatus('PREVIEW');
				$('#deploy-result-content').html('Error deploying... (AJAX error)');
			})
	});
});