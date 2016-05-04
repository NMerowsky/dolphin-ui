function toggleLoadingModal(command){
	$('#pleaseWaitDialog').modal(command);
}

$(function() {
	var modal = document.getElementById('pleaseWaitDialog');
	var backdrop = document.getElementById('backdrop');
	backdrop.setAttribute("class", "modal-backdrop hide");
	modal.setAttribute("class", "modal fade hide");
});
