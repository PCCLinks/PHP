	$(function() {
		$('#panel2').slidePanel({
			triggerName: '#trigger2',
			triggerTopPos: '',
			panelTopPos: '45px',
			speed: 'slow',
			ajax: true,
			ajaxSource: 'common/form_search.php'
		});
	});