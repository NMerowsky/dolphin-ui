$(function() {
	toggleLoadingModal('show');
	
	$(window).load(function(){
		toggleLoadingModal('hide');
	})
});