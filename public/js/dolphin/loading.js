function toggleLoadingModal(command){
	$('#pleaseWaitDialog').modal(command);
}

$(function() {
	toggleLoadingModal('show');
});