
function obtainEncodeDonor() {
	var obj = undefined;
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/encode_tables.php",
		data: { p: 'obtainDonors' },
		async: false,
		success : function(s)
		{
			console.log(s);
			obj = s;
		}
	});
	return obj
}

function obtainEncodeSample(){
	var obj = undefined;
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/encode_tables.php",
		data: { p: 'obtainSamples' },
		async: false,
		success : function(s)
		{
			console.log(s);
			obj = s;
		}
	});
	return obj
}

function obtainEncodeTreatment(){
	var obj = undefined;
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/encode_tables.php",
		data: { p: 'obtainTreatments' },
		async: false,
		success : function(s)
		{
			console.log(s);
			obj = s;
		}
	});
	return obj
}

function obtainEncodeAntibody(){
	var obj = undefined;
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/encode_tables.php",
		data: { p: 'obtainAntibodies' },
		async: false,
		success : function(s)
		{
			console.log(s);
			obj = s;
		}
	});
	return obj
}

function obtainEncodeFiles(){
	var obj = undefined;
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/encode_tables.php",
		data: { p: 'obtainFiles' },
		async: false,
		success : function(s)
		{
			console.log(s);
			obj = s;
		}
	});
	return obj
}

$(function() {
	"use strict";
	
	createStreamTable('donor_info', obtainEncodeDonor(), "", true, [20,50], 20, true, true);
	createStreamTable('sample_info', obtainEncodeSample(), "", true, [20,50], 20, true, true);
	createStreamTable('treatment_info', obtainEncodeTreatment(), "", true, [20,50], 20, true, true);
	createStreamTable('antibodby_lot_info', obtainEncodeAntibody(), "", true, [20,50], 20, true, true);
	createStreamTable('file_info', obtainEncodeFiles(), "", true, [20,50], 20, true, true);
	
});
