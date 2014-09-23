$(function () {
	var getLatestRevisionLink = $('#get-latest-revision');
	var projectName = getLatestRevisionLink.attr('data-project');
	var originalLinkText = getLatestRevisionLink.html();
	
	getLatestRevisionLink.click(function (e) {
		if (getLatestRevisionLink.hasClass('in-progress')) return;

		getLatestRevisionLink.addClass('in-progress');
		getLatestRevisionLink.html('Please wait, fetching latest revision...');
		
		$.ajax({
			url: '/get-latest-revision/' + projectName,
			dataType: 'json'
		})
			.done(function (result) {
				$('#deploy-revision').attr('value', result.latestCommit);
				getLatestRevisionLink.html(originalLinkText);
				getLatestRevisionLink.removeClass('in-progress');
			})
			.fail(function () {
				getLatestRevisionLink.removeClass('in-progress');
				alert('AJAX error fetching revision...');
			})
	});
});