function toggleLoadingModal(command){
	$('#pleaseWaitDialog').modal(command);
}

$(function() {
	console.log('test');
	$('#pleaseWaitDialog').modal('show');
	
	console.log('test');
	
	$(window).load(function(){
		$('#pleaseWaitDialog').modal('hide');
	})
});