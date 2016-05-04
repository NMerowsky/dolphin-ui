function toggleLoadingModal(command){
	$('#pleaseWaitDialog').modal(command);
}

$(function() {
	$(window).load(function(){
		toggleLoadingModal('hide');
	})
});