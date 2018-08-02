jQuery(document).on('click', '.mtsnb-notice-dismiss', function(e){
	e.preventDefault();
	var $this = jQuery(this);
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: {
			action: 'mts_dismiss_nb_notice',
			dismiss: jQuery(this).data('ignore')
		}
	}).done(function() {
		$this.parent().remove();
	});
	return false;
});