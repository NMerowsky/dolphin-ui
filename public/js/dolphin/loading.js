function toggleLoadingModal(command){
	$('#pleaseWaitDialog').modal(command);
}

$(function() {
	toggleLoadingModal('show');
	
	/*
	$(window).load(function(){
		toggleLoadingModal('hide');
	})
	*/
});