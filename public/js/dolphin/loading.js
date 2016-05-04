function toggleLoadingModal(command){
	$('#pleaseWaitDialog').modal(command);
}

$(function() {
	$.ajax({
		beforeSend: function(){
			$('#pleaseWaitDialog').modal('show');
		},
		complete: function(){
			$('#pleaseWaitDialog').modal('hide');
		}
	});
});