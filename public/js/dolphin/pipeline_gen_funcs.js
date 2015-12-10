/*
 *Author: Nicholas Merowsky
 *Date: 09 Apr 2015
 *Ascription:
 */

//GLOBAL ID DICTIONARY
var ID_DICTIONARY = {};
var STORED_SAMPLE_DATA = [];

//GLOBAL VARIABLES
var jsonTypeList = ['genomebuild', 'spaired', 'resume', 'barcodes', 'fastqc', 'adapter', 'submission', 'quality', 'trim', 'commonind', 'split', 'pipeline', 'advparams', 'custominds'];
var radioTypeCheckList = ['pipeline', 'trimpaired', 'advparams', 'custom'];
var currentChecked = "";
var checklist_samples = [];
var checklist_lanes = [];
var checklist_experiment_series = [];
var pipelineNum = 0;
var customSeqNum = 0;
var customSeqNumCheck = [];
var pipelineDict = ['RNASeqRSEM', 'Tophat', 'ChipSeq', 'DESeq', 'BisulphiteMapping'];
var rnaList = ["ercc","rRNA","miRNA","tRNA","piRNA","snRNA","rmsk","genome","change_params"];
var qualityDict = ["window size","required quality","leading","trailing","minlen"];
var trimmingDict = ["single or paired-end", "5 length 1", "3 length 1", "5 length 2", "3 length 2"];
var currentPipelineID = [];
var currentPipelineVal =[];
var rsemSwitch = false;
var deseqList = ['RSEM'];
var valid_samples;
var username;

/*##### FILL A RERUN PIPELINE WITH PREVIOUS SELECTIONS #####*/
function rerunLoad() {
	var hrefSplit = window.location.href.split("/");
	if (hrefSplit[4] == 'search') {
		document.getElementById('dolphin_basket').parentNode.setAttribute('style','overflow:scroll');
		var basket = $('#dolphin_basket').dataTable( {
			//"bFilter":	false,
			//"ordering": false,
			//"info":     false,
			"lengthChange": false,
			"pageLength": 20,
			"columnDefs": [ { "targets": 2, "orderable": false } ]
		} );
	}
	var rerunLoc = $.inArray('rerun', hrefSplit);
	var infoArray = [];
	var json_primer = '';
	var jsonObj;

	//make sure this is a rerun
	if (rerunLoc != -1) {
	infoArray = grabReload(hrefSplit[rerunLoc + 1]);
	json_primer = infoArray[0];
	console.log(json_primer);
	jsonObj = JSON.parse(json_primer);
	//repopulate page
	for (var x = 0; x < (jsonTypeList.length); x++) {
		if (jsonObj.hasOwnProperty(jsonTypeList[x]) && jsonObj[jsonTypeList[x]] != 'none' && jsonObj[jsonTypeList[x]] != 'NONE' && jsonObj[jsonTypeList[x]] != 'NO' && jsonTypeList[x] != 'barcodes') {
			var element = document.getElementById(jsonTypeList[x]);
			if (element != null) {
				if (element.id == "spaired") {
					if ( jsonObj[jsonTypeList[x]] == 'paired') {
						element.value = 'yes'
					}else{
						element.value = 'no';
					}
				}else if (element.id == "resume"){
					element.value = 'Resume';
				}else if (element.id == "0"){
					element.value = 'None';
				}else if (element.id == "1"){
					element.value = 'Encode';
				}else if (element.id == "2"){
					element.value = 'Geo';
				}else{
					element.value = jsonObj[jsonTypeList[x]];
				}
			}else{
				//try radio
				if (radioTypeCheckList.indexOf(jsonTypeList[x]) == -1 && jsonTypeList[x] != 'custominds') {
					//expand the altered fields
					$( '#'+jsonTypeList[x]+'_yes' ).iCheck('check');
					document.getElementById(jsonTypeList[x]+'_exp').setAttribute('class', 'box box-default');
					document.getElementById(jsonTypeList[x]+'_exp_btn').setAttribute('class', 'fa fa-minus');
					document.getElementById(jsonTypeList[x]+'_exp_body').setAttribute('style', 'display: block');
					
					//fill the fields that have been expanded
					if (Array.isArray(jsonObj[jsonTypeList[x]])) {
						var splt1 = jsonObj[jsonTypeList[x]][0];
					}else{
						var splt1 = jsonObj[jsonTypeList[x]].split(":");
					}
					if (Array.isArray(splt1) && splt1.length == 1 && jsonTypeList[x] != 'commonind') {
						if (jsonTypeList[x] == 'split') {
							document.getElementById('number of reads per file_val').value = jsonObj[jsonTypeList[x]];
						}else{
							document.getElementById(jsonTypeList[x]+'_val').value = jsonObj[jsonTypeList[x]].replace(/__cr____cn__/g, "\n");;
						}
					}else{
						if (jsonTypeList[x] == 'quality') {
							document.getElementById( qualityDict[0]+'_val' ).value = splt1.windowSize;
							document.getElementById( qualityDict[1]+'_val' ).value = splt1.requiredQuality;
							document.getElementById( qualityDict[2]+'_val' ).value = splt1.leading;
							document.getElementById( qualityDict[3]+'_val' ).value = splt1.trailing;
							document.getElementById( qualityDict[4]+'_val' ).value = splt1.minlen;
						}else if (jsonTypeList[x] == 'trim'){
							if (splt1.hasOwnProperty('trimpaired')) {
								document.getElementById( trimmingDict[0]+'_val').value = 'paired-end';
								selectTrimming('single or paired-end_val', 0, 0);
								document.getElementById( trimmingDict[1]+'_val' ).value = splt1['5len1'];
								document.getElementById( trimmingDict[2]+'_val' ).value = splt1['3len1'];
								document.getElementById( trimmingDict[3]+'_val' ).value = splt1['5len2'];
								document.getElementById( trimmingDict[4]+'_val' ).value = splt1['3len2'];
							}else if(z == 0 && !jsonObj.hasOwnProperty('trimpaired')){
								document.getElementById( trimmingDict[0]+'_val').value = 'single-end';
								document.getElementById( trimmingDict[1]+'_val' ).value = splt1['5len1'];
								document.getElementById( trimmingDict[2]+'_val' ).value = splt1['3len1'];
							}
								
						}else if (jsonTypeList[x] == 'commonind'){
							var splt2 = splt1[0].split(",");
							for(var y = 0; y < splt2.length; y++){
								$( '#'+splt2[y]+'_yes' ).iCheck('check');
								deseqList.push(splt2[y]);
							}
						}else{
							console.log(splt1);
						}
					}
				}else if (jsonTypeList[x] == 'advparams') {
					changeRNAParamsBtn();
					document.getElementById('change_params_val').value = jsonObj[jsonTypeList[x]];
				}else if (jsonTypeList[x] == 'custominds') {
					document.getElementById('custom_exp').setAttribute('class', 'box box-default');
					document.getElementById('custom_exp_btn').setAttribute('class', 'fa fa-minus');
					for(var y = 0; y < jsonObj[jsonTypeList[x]].length; y++){
						sequenceSetsBtn();
						document.getElementById('custom_1_'+y).value = jsonObj[jsonTypeList[x]][y].FullPath;
						document.getElementById('custom_2_'+y).value = jsonObj[jsonTypeList[x]][y].IndexPrefix;
						document.getElementById('custom_3_'+y).value = jsonObj[jsonTypeList[x]][y].BowtieParams;
						document.getElementById('custom_4_'+y).value = jsonObj[jsonTypeList[x]][y].Description;
						document.getElementById('custom_5_'+y).value = jsonObj[jsonTypeList[x]][y]['Filter Out'];
					}
					document.getElementById('custom_exp_body').setAttribute('style', 'display: block');
				}else{
					//pipeline
					document.getElementById(jsonTypeList[x]+'_exp').setAttribute('class', 'box box-default');
					document.getElementById(jsonTypeList[x]+'_exp_btn').setAttribute('class', 'fa fa-minus');
	
					var splt1 = jsonObj[jsonTypeList[x]];
					for (var i = 0; i < splt1.length; i++){
						if (splt1[i].Type == pipelineDict[0]) {
							//RSEM
							additionalPipes();
							document.getElementById('select_'+i).value = pipelineDict[0];
							pipelineSelect(i);
							document.getElementById('textarea_'+i).value = splt1[i].Params;
							document.getElementById('select_1_'+i).value = splt1[i].IGVTDF;
							document.getElementById('select_2_'+i).value = splt1[i].BAM2BW;
							if (splt1[i].ExtFactor != '0' && splt1[i].ExtFactor != 0) {
								IGVTDFSelection('select_1_'+i);
								document.getElementById('textarea_2_'+i).value = splt1[i].ExtFactor;
							}
							if (splt1[i].RSeQC == 'yes') {
								document.getElementById('checkbox_1_'+i).checked = true;
							}
							rsemSwitch = true;
						}else if (splt1[i].Type == pipelineDict[1]) {
							//Tophat
							additionalPipes();
							document.getElementById('select_'+i).value = pipelineDict[1];
							pipelineSelect(i);
							document.getElementById('textarea_'+i).value = splt1[i].Params;
							document.getElementById('select_1_'+i).value = splt1[i].IGVTDF;
							document.getElementById('select_2_'+i).value = splt1[i].BAM2BW;
							if (splt1[i].ExtFactor != '0' && splt1[i].ExtFactor != 0) {
								IGVTDFSelection('select_1_'+i);
								document.getElementById('textarea_2_'+i).value = splt1[i].ExtFactor;
							}
							if (splt1[i].RSeQC == 'yes') {
								document.getElementById('checkbox_1_'+i).checked = true;
							}
							if (splt1[i].CollectRnaSeqMetrics == 'yes') {
								document.getElementById('checkbox_2_'+i).checked = true;
							}
							if (splt1[i].CollectMultipleMetrics == 'yes') {
								document.getElementById('checkbox_3_'+i).checked = true;
							}
							if (splt1[i].MarkDuplicates == 'yes') {
								document.getElementById('checkbox_4_'+i).checked = true;
							}
						}else if (splt1[i].Type == pipelineDict[2]){
							//Chipseq
							additionalPipes();
							document.getElementById('select_'+i).value = pipelineDict[2];
							pipelineSelect(i);
							document.getElementById('textarea_'+i).value = splt1[i].ChipInput.replace(/__cr____cn__/g, "\n");
							document.getElementById('text_1_'+i).value = splt1[i].MultiMapper;
							document.getElementById('text_2_'+i).value = splt1[i].TagSize;
							document.getElementById('select_1_'+i).value = splt1[i].BandWith;
							document.getElementById('select_2_'+i).value = splt1[i].EffectiveGenome;
							document.getElementById('select_3_'+i).value = splt1[i].IGVTDF;
							document.getElementById('select_4_'+i).value = splt1[i].BAM2BW;
							if (splt1[i].ExtFactor != '0' && splt1[i].ExtFactor != 0) {
								IGVTDFSelection('select_3_'+i);
								document.getElementById('textarea_2_'+i).value = splt1[i].ExtFactor;
							}
							if (splt1[i].CollectMultipleMetrics == 'yes') {
								document.getElementById('checkbox_1_'+i).checked = true;
							}
							if (splt1[i].MarkDuplicates == 'yes') {
								document.getElementById('checkbox_2_'+i).checked = true;
							}
						}else if (splt1[i].Type == pipelineDict[3]) {
							//DESEQ
							additionalPipes();
							document.getElementById('select_'+i).value = pipelineDict[3];
							pipelineSelect(i);
							
							//handle for multiple selections
							document.getElementById('text_1_'+i).value = splt1[i].Name;
							var select_values = [];
							var select_locations = [];
							if (splt1[i].Columns != undefined) {
								select_values = splt1[i].Columns.split(",");
								select_locations = splt1[i].Conditions.split(",");
							}
							var select1_values = [];
							var select2_values = [];
							for(var f = 0; f < select_locations.length; f++){
								if (select_locations[f] == 'Cond1') {
									select1_values.push(select_values[f]);
								}else{
									select2_values.push(select_values[f]);
								}
							}
							
							var select1 = document.getElementById('multi_select_1_'+i);
							for(var h = 0; h < select1.options.length; h++){
								if (select1_values.indexOf(select1.options[h].value) != -1) {
									select1.options[h].selected = true;
								}
							}
							var select2 = document.getElementById('multi_select_2_'+i);
							for(var h = 0; h < select1.options.length; h++){
								if (select2_values.indexOf(select2.options[h].value) != -1) {
									select2.options[h].selected = true;
								}
							}
							document.getElementById('select_3_'+i).value = splt1[i].FitType;
							document.getElementById('select_4_'+i).value = splt1[i].HeatMap;
							document.getElementById('text_2_'+i).value = splt1[i].padj;
							document.getElementById('text_3_'+i).value = splt1[i].foldChange;
							document.getElementById('select_5_'+i).value = splt1[i].DataType;
						}else if (splt1[i].Type == pipelineDict[4]) {
							//MMap
							additionalPipes();
							document.getElementById('select_'+i).value = pipelineDict[4];
							pipelineSelect(i);
							
							document.getElementById('text_1_'+i).value = splt1[i].Digestion;
							$("#" + i.toString() + "_" + splt1[i].BisulphiteType).iCheck('check');
							if (splt1[i].BisulphiteType == 'WGBS') {
								document.getElementById('text_1_'+i).disabled = true;
							}
							document.getElementById('textarea_1_'+i).value = splt1[i].BSMapParams;
							document.getElementById('select_1_'+i).value = splt1[i].IGVTDF;
							document.getElementById('select_2_'+i).value = splt1[i].BAM2BW;
							if (splt1[i].ExtFactor != '0' && splt1[i].ExtFactor != 0) {
								IGVTDFSelection('select_1_'+i);
								document.getElementById('textarea_2_'+i).value = splt1[i].ExtFactor;
							}
							if (splt1[i].CollectMultipleMetrics == 'yes') {
								document.getElementById('checkbox_2_'+i).checked = true;
							}
							if (splt1[i].MarkDuplicates == 'yes') {
								document.getElementById('checkbox_3_'+i).checked = true;
							}
							
							//MCall
							//handle for multiple selections
							if (splt1[i].MCallStep == 'yes') {
								document.getElementById('checkbox_4_'+i).checked = true;
							}
							MCallSelection(i);
							var select_values = [];
							var select_locations = [];
							if (splt1[i].Columns != undefined) {
								select_values = splt1[i].Columns.split(",");
								select_locations = splt1[i].Conditions.split(",");
							}
							var select1_values = [];
							var select2_values = [];
							for(var f = 0; f < select_locations.length; f++){
								if (select_locations[f] == 'Cond1') {
									select1_values.push(select_values[f]);
								}else{
									select2_values.push(select_values[f]);
								}
							}
							
							var select1 = document.getElementById('multi_select_1_'+i);
							for(var h = 0; h < select1.options.length; h++){
								if (select1_values.indexOf(select1.options[h].value) != -1) {
									select1.options[h].selected = true;
								}
							}
							var select2 = document.getElementById('multi_select_2_'+i);
							for(var h = 0; h < select1.options.length; h++){
								if (select2_values.indexOf(select2.options[h].value) != -1) {
									select2.options[h].selected = true;
								}
							}
							document.getElementById('textarea_3_'+i).value = splt1[i].MCallParams;
							
							//MComp
							if (splt1[i].MCompStep == 'yes') {
								document.getElementById('checkbox_5_'+i).checked = true;
							}
							MCompSelection(i);
							document.getElementById('textarea_4_'+i).value = splt1[i].MCompParams;
						}
					}
					document.getElementById(jsonTypeList[x]+'_exp_body').setAttribute('style', 'display: block');
				}
			}
		}
	}
	document.getElementById('outdir').value = infoArray[1];
	document.getElementById('run_name').value = infoArray[2];
	document.getElementById('run_description').value = infoArray[3];
	}
}

/*##### SELECT/FILL PIPELINE #####*/
//used to generate divs within the html for the additional pipelines
function pipelineSelect(num){
	//Grab some useful variables
	var pipeType = document.getElementById('select_'+num).value;
	var divAdj = createElement('div', ['id', 'class', 'style'], ['select_child_'+num, 'input-group margin col-md-11', 'float:left']);
	//Check for only one RSEM/DESeq dependencies
	if (pipeType == pipelineDict[0] && rsemSwitch)
	{
		$('#errorModal').modal({
			show: true
		});
		document.getElementById('errorLabel').innerHTML ='You cannot select more than one additional RSEM pipeline';
		document.getElementById('errorAreas').innerHTML = '';
		document.getElementById('select_'+num).value = currentPipelineVal[currentPipelineID.indexOf(num)];
	}
	else if (pipeType == pipelineDict[3] && !rsemSwitch) {
		$('#errorModal').modal({
			show: true
		});
		document.getElementById('errorLabel').innerHTML ='You must first add a RSEM pipeline before running DESeq';
		document.getElementById('errorAreas').innerHTML = '';
		document.getElementById('select_'+num).value = currentPipelineVal[currentPipelineID.indexOf(num)];
	}
	else
	{
	//pipelineDict: global variable containing selections
	if (pipeType == pipelineDict[0]) {
		//RNASeq RSEM
		divAdj.appendChild( createElement('label', ['class','TEXTNODE'], ['box-title', 'RSEM parameters:']));
		var testText = createElement('textarea', ['id', 'class'], ['textarea_'+num,'form-control'])
		testText.value = '--bowtie-e 70 --bowtie-chunkmbs 100';
		divAdj.appendChild( testText );
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'IGV/TDF Conversion:']),
				createElement('select', ['id','class','onChange','OPTION', 'OPTION'], ['select_1_'+num, 'form-control', 'IGVTDFSelection(this.id)','no', 'yes'])],

				[createElement('label', ['class','TEXTNODE'], ['box-title', 'BigWig Conversion:']),
				createElement('select', ['id', 'class', 'OPTION', 'OPTION'], ['select_2_'+num, 'form-control', 'no', 'yes'])] ]);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['id', 'class', 'style', 'TEXTNODE'], ['label_1_'+num, 'box-title', 'display:none', 'extFactor']),
				   createElement('input', ['id', 'class', 'type', 'style', 'value'], ['textarea_2_'+num, 'form-control', 'text', 'display:none', '0'])] ]);
		divAdj = mergeTidy(divAdj, 12,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title margin', 'RNA-Seq QC:']),
				   createElement('input', ['id', 'type', 'class'], ['checkbox_1_'+num, 'checkbox', 'margin'])] ]);
		rsemSwitch = true;
	}else if (pipeType == pipelineDict[1]) {
		//Tophat Pipeline
		divAdj.appendChild( createElement('label', ['class','TEXTNODE'], ['box-title', 'Tophat parameters:']));
		divAdj.appendChild( createElement('textarea', ['id', 'class'], ['textarea_'+num, 'form-control']));
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'IGV/TDF Conversion:']),
				createElement('select', ['id','class','onChange','OPTION', 'OPTION'], ['select_1_'+num, 'form-control', 'IGVTDFSelection(this.id)','no', 'yes'])],
				[createElement('label', ['class','TEXTNODE'], ['box-title', 'BigWig Conversion:']),
				createElement('select', ['id', 'class', 'OPTION', 'OPTION'], ['select_2_'+num, 'form-control', 'no', 'yes'])] ]);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['id', 'class', 'style', 'TEXTNODE'], ['label_1_'+num, 'box-title', 'display:none', 'extFactor']),
				   createElement('input', ['id', 'class', 'type', 'style', 'value'], ['textarea_2_'+num, 'form-control', 'text', 'display:none', '0'])] ]);
		divAdj = mergeTidy(divAdj, 12,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title margin', 'RNA-Seq QC:']),
				   createElement('input', ['id', 'type', 'class'], ['checkbox_1_'+num, 'checkbox', 'margin'])] ]);
		divAdj = mergeTidy(divAdj, 12,
				[ [createElement('label', ['class','TEXTNODE'], ['margin', 'Collect RNA Metrics']),
				createElement('input', ['id', 'type', 'class'], ['checkbox_2_'+num, 'checkbox', 'margin'])],
				[createElement('label', ['class','TEXTNODE'], ['margin', 'Collect Multiple Picard Metrics']),
				createElement('input', ['id', 'type', 'class'], ['checkbox_3_'+num, 'checkbox', 'margin'])],
				[createElement('label', ['class','TEXTNODE'], ['margin', 'Mark Duplicates']),
				createElement('input', ['id', 'type', 'class'], ['checkbox_4_'+num, 'checkbox', 'margin'])] ]);
	}else if (pipeType == pipelineDict[2]) {
		//ChipSeq Pipeline
		divAdj.appendChild( createElement('label', ['class','TEXTNODE'], ['box-title', 'Chip Input Definitions:']));
		divAdj.appendChild( createElement('textarea', ['id', 'class'], ['textarea_'+num, 'form-control']));
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'Multimapper:']),
				createElement('input', ['id', 'class', 'type', 'value'], ['text_1_'+num, 'form-control', 'text', '1'])],
				[createElement('label', ['class','TEXTNODE'], ['box-title', 'Tag size(bp) for MACS:']),
				createElement('input', ['id', 'class', 'type', 'value'], ['text_2_'+num, 'form-control', 'text', '75'])] ]);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'Band width(bp) for MACS:']),
				createElement('input', ['id', 'class', 'type', 'value'], ['select_1_'+num, 'form-control', 'text', '230'])],
				[createElement('label', ['class','TEXTNODE'], ['box-title', 'Effective genome size(bp):']),
				createElement('input', ['id', 'class', 'type', 'value'], ['select_2_'+num, 'form-control', 'text', '2700000000'])] ]);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'IGV/TDF Conversion:']),
				createElement('select', ['id','class','onChange','OPTION', 'OPTION'], ['select_3_'+num, 'form-control', 'IGVTDFSelection(this.id)','no', 'yes'])],
				[createElement('label', ['class','TEXTNODE'], ['box-title', 'BigWig Conversion:']),
				createElement('select', ['id', 'class', 'OPTION', 'OPTION'], ['select_4_'+num, 'form-control', 'no', 'yes'])] ]);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['id', 'class', 'style', 'TEXTNODE'], ['label_1_'+num, 'box-title', 'display:none', 'extFactor']),
				   createElement('input', ['id', 'class', 'type', 'style', 'value'], ['textarea_2_'+num, 'form-control', 'text', 'display:none', '0'])] ]);
		divAdj = mergeTidy(divAdj, 12,
				[ [createElement('label', ['class','TEXTNODE'], ['margin', 'Collect Multiple Picard Metrics:']),
				createElement('input', ['id', 'type', 'class'], ['checkbox_1_'+num, 'checkbox', 'margin'])],
				[createElement('label', ['class','TEXTNODE'], ['margin', 'Mark Duplicates:']),
				createElement('input', ['id', 'type', 'class'], ['checkbox_2_'+num, 'checkbox', 'margin'])] ]);
	}else if (pipeType == pipelineDict[3]) {
		//DESEQ
		divAdj = mergeTidy(divAdj, 12,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'Name:']),
				createElement('input', ['id', 'class', 'type', 'value'], ['text_1_'+num, 'form-control', 'text', ''])] ]);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'Condition 1']),
				createElement('select',['id', 'class', 'type', 'multiple', 'size', 'onchange'],['multi_select_1_'+num, 'form-control', 'select-multiple', 'multiple', '8', 'deselectCondition(1, '+num+')'])],
				[createElement('label', ['class','TEXTNODE'], ['box-title', 'Condition 2']),
				createElement('select',['id', 'class', 'type', 'multiple', 'size', 'onchange'],['multi_select_2_'+num, 'form-control', 'select-multiple', 'multiple', '8', 'deselectCondition(2, '+num+')'])] ]);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'Fit Type:']),
				createElement('select', ['id', 'class', 'OPTION', 'OPTION', 'OPTION'], ['select_3_'+num, 'form-control', 'parametric', 'local', 'mean'])],
				[createElement('label', ['class','TEXTNODE'], ['box-title', 'Heatmap:']),
				createElement('select', ['id', 'class', 'OPTION', 'OPTION'], ['select_4_'+num, 'form-control', 'Yes', 'No'])] ]);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'pAdj cutoff']),
				createElement('input', ['id', 'class', 'type', 'value'], ['text_2_'+num, 'form-control', 'text', '0.01'])],
				[createElement('label', ['class','TEXTNODE'], ['box-title', 'Fold Change cutoff']),
				createElement('input', ['id', 'class', 'type', 'value'], ['text_3_'+num, 'form-control', 'text', '2'])] ]);
		divAdj = mergeTidy(divAdj, 12,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'Select Sequence']),
				createElement('select', ['id', 'class'], ['select_5_'+num, 'form-control'])] ]);
	}else if (pipeType == pipelineDict[4]) {
		//MMap
		var labelDiv = createElement('div', ['class'], ['col-md-12 text-center']);
		labelDiv.appendChild( createElement('label', ['class','TEXTNODE'], ['box-title margin', 'Run BSMap:']));
		labelDiv.appendChild( createElement('input', ['id', 'type', 'class', 'checked', 'disabled'], ['checkbox_1_'+num, 'checkbox', 'margin']));
		divAdj.appendChild(labelDiv);
		var innerDiv = createTidyDiv(12);
		divAdj = mergeTidy(divAdj, 12,
				[[createLabeledDiv(12, 'RRBS&nbsp;&nbsp;', '&nbsp;&nbsp;WGBS', createElement('input', ['id', 'name', 'type', 'value', 'onClick', 'checked'], [num+'_RRBS', num, 'radio', 'RRBS', 'bisulphiteSelect(this.id, '+num+')']),
					   createElement('input', ['id', 'name', 'type', 'value', 'onClick',], [num+'_WGBS', num, 'radio', 'WGBS', 'bisulphiteSelect(this.id, '+num+')']))
					],
				[createElement('label', ['class','TEXTNODE'], ['box-title', 'Digestion Site:']),
				createElement('input', ['id', 'class', 'type', 'value'], ['text_1_'+num, 'form-control', 'text', 'C-CGG'])]
				]);
		labelDiv = createElement('div', ['class'], ['col-md-12']);
		labelDiv.appendChild( createElement('label', ['class','TEXTNODE'], ['box-title', 'Additional BSMap Parameters:']));
		labelDiv.appendChild( createElement('textarea', ['id', 'class'], ['textarea_1_'+num, 'form-control']));
		divAdj.appendChild(labelDiv);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['class','TEXTNODE'], ['box-title', 'IGV/TDF Conversion:']),
				createElement('select', ['id','class','onChange','OPTION', 'OPTION'], ['select_1_'+num, 'form-control', 'IGVTDFSelection(this.id)','no', 'yes'])],
				[createElement('label', ['class','TEXTNODE'], ['box-title', 'BigWig Conversion:']),
				createElement('select', ['id', 'class', 'OPTION', 'OPTION'], ['select_2_'+num, 'form-control', 'no', 'yes'])] ]);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['id', 'class', 'style', 'TEXTNODE'], ['label_1_'+num, 'box-title', 'display:none', 'extFactor']),
				   createElement('input', ['id', 'class', 'type', 'style', 'value'], ['textarea_2_'+num, 'form-control', 'text', 'display:none', '0'])] ]);
		divAdj = mergeTidy(divAdj, 12,
				[ [createElement('label', ['class','TEXTNODE'], ['margin', 'Collect Multiple Picard Metrics:']),
				createElement('input', ['id', 'type', 'class'], ['checkbox_2_'+num, 'checkbox', 'margin'])],
				[createElement('label', ['class','TEXTNODE'], ['margin', 'Mark Duplicates:']),
				createElement('input', ['id', 'type', 'class'], ['checkbox_3_'+num, 'checkbox', 'margin'])] ]);
		
		//MCALL
		labelDiv = createElement('div', ['class'], ['col-md-12 text-center']);
		labelDiv.appendChild( createElement('label', ['class','TEXTNODE'], ['box-title margin', 'Run MCall:']));
		labelDiv.appendChild( createElement('input', ['id', 'type', 'class', 'onClick'], ['checkbox_4_'+num, 'checkbox', 'margin', 'MCallSelection("'+num+'")']));
		divAdj.appendChild(labelDiv);
		divAdj = mergeTidy(divAdj, 6,
				[ [createElement('label', ['id', 'class', 'style', 'TEXTNODE'], ['label_2_'+num, 'box-title', 'display:none', 'MCall Condition 1']),
				createElement('select',['id', 'class', 'type', 'multiple', 'size', 'style', 'onchange'],['multi_select_1_'+num, 'form-control', 'select-multiple', 'multiple', '8', 'display:none', 'deselectCondition(1, '+num+')'])],
				[createElement('label', ['id', 'class', 'style', 'TEXTNODE'], ['label_3_'+num, 'box-title', 'display:none', 'MCall Condition 2']),
				createElement('select',['id', 'class', 'type', 'multiple', 'size', 'style', 'onchange'],['multi_select_2_'+num, 'form-control', 'select-multiple', 'multiple', '8', 'display:none', 'deselectCondition(2, '+num+')'])] ]);
		labelDiv = createElement('div', ['class'], ['col-md-12']);
		labelDiv.appendChild( createElement('label', ['id', 'class', 'style', 'TEXTNODE'], ['label_4_'+num, 'box-title', 'display:none', 'Additional MCall Parameters:']));
		labelDiv.appendChild( createElement('textarea', ['id', 'class', 'style'], ['textarea_3_'+num, 'form-control', 'display:none']));
		divAdj.appendChild(labelDiv);
		
		//MComp
		labelDiv = createElement('div', ['class'], ['col-md-12 text-center']);
		labelDiv.appendChild( createElement('label', ['class','TEXTNODE'], ['box-title margin', 'Run MComp:']));
		labelDiv.appendChild( createElement('input', ['id', 'type', 'class', 'onClick'], ['checkbox_5_'+num, 'checkbox', 'margin', 'MCompSelection("'+num+'")']));
		divAdj.appendChild(labelDiv);
		labelDiv = createElement('div', ['class'], ['col-md-12']);
		labelDiv.appendChild( createElement('label', ['id', 'class', 'style', 'TEXTNODE'], ['label_5_'+num, 'box-title', 'display:none', 'Additional MComp Parameters:']));
		labelDiv.appendChild( createElement('textarea', ['id', 'class', 'style'], ['textarea_4_'+num, 'form-control', 'display:none']));
		divAdj.appendChild(labelDiv);
	}
	//replace div
	$('#select_child_'+num).replaceWith(divAdj);
	if (pipeType == pipelineDict[3]) {
		for (var x = 0; x < deseqList.length; x++) {
			var opt = createElement('option', ['id', 'value'], [deseqList[x], deseqList[x]]);
			opt.innerHTML = deseqList[x];
			document.getElementById('select_5_'+num).appendChild(opt);
		}
	}
	
	//MULTI-SELECT
	if (document.getElementById('multi_select_1_'+num) != null) {
		var sample_names = getSampleNames(window.location.href.split('/')[window.location.href.split('/').length - 1].replace('$', ''));
		console.log(sample_names);
		for(var x = 0; x < sample_names.length; x++){
				document.getElementById('multi_select_1_'+num).appendChild(createElement('option', ['id', 'value'], [num+'_1_'+sample_names[x], sample_names[x]]));
				document.getElementById(num+'_1_'+sample_names[x]).innerHTML = sample_names[x]
				document.getElementById('multi_select_2_'+num).appendChild(createElement('option', ['id', 'value'], [num+'_2_'+sample_names[x], sample_names[x]]));
				document.getElementById(num+'_2_'+sample_names[x]).innerHTML = sample_names[x]
		}
	}
	
	//adjust global pipeline counter
	if (currentPipelineID.indexOf(num) == -1) {
		currentPipelineID.push(num);
		currentPipelineVal.push(pipeType);
	}
	else if (currentPipelineID.indexOf(num) != -1 && currentPipelineVal.indexOf(currentPipelineID.indexOf(num)) != pipeType)
	{
		if (currentPipelineVal[currentPipelineID.indexOf(num)] == pipelineDict[0]) {
		rsemSwitch = false;
		}
		currentPipelineVal[currentPipelineID.indexOf(num)] = pipeType;
	}
	}
}

/*##### SUBMIT PIPELINE RUN #####*/
function submitPipeline(type) {
	//Static
	var genome = document.getElementById("genomebuild").value;
	var matepair = document.getElementById("spaired").value;
	var freshrun = document.getElementById("resume").value;
	var outputdir = document.getElementById("outdir").value;
	var fastqc = document.getElementById("fastqc").value;
	var run_name = document.getElementById("run_name").value;
	var description = document.getElementById("run_description").value;
	var perms = document.getElementById("perms").value;
	var group = document.getElementById("groups").value;
	var submission = document.getElementById("submission").value;
	
	var JSON_OBJECT = {};
	var empty_values = []
	if (run_name == "") {
		empty_values.push('Run Name');
	}
	if (description == "") {
		empty_values.push("Run Description");
	}
	if (outputdir == "") {
		empty_values.push('Output Directory');
	}
	
	if (empty_values.length > 0) {
		$('#errorModal').modal({
			show: true
		});
		document.getElementById('errorLabel').innerHTML ='The following fields may not be empty for submission:';
		document.getElementById('errorAreas').innerHTML = empty_values.join(", ");
	}else{
		//	get Username
		$.ajax({
			type: 	'GET',
			url: 	BASE_PATH+'/public/ajax/ngsfastlanedb.php',
			data:  	{ p: 'getClusterName' },
			async:	false,
			success: function(s)
			{
				username = JSON.parse(s)[0];
			}
		});
		
		//Expanding
		var doAdapter = findRadioChecked("adapter");
		var doQuality = findRadioChecked("quality");
		var doTrimming = findRadioChecked("trim");
		var doRNA = findRNAChecked(rnaList);
		var doSplit = findRadioChecked("split");
	
		var adapter = findAdditionalInfoValues(doAdapter, ["adapter"]);

		var adapterCheck = false;
		if (adapter[0].match(/[bd-fh-sv-zBD-FH-SV-Z0-9\!\@\#\$\%\^\&\*\(\)\_\+\-\=\\\|\[\]\{\}\;\'\:\"\,\.\/\<\>\?\`\~]+/g)) {
			adapterCheck = true;
		}
			
		var quality = findAdditionalInfoValues(doQuality, ["window size", "required quality", "leading", "trailing", "minlen"]);
		var trimming = findAdditionalInfoValues(doTrimming, ["single or paired-end", "5 length 1", "3 length 1", "5 length 2", "3 length 2"]);
		var rna = findAdditionalInfoValues(doRNA, rnaList);
		var split = findAdditionalInfoValues(doSplit, ["number of reads per file"]);
	
		//Pipeline
		var pipelines = findPipelineValues();
	
		//Grab sample ids
		var ids = getSampleIDs(phpGrab.theSearch);
		var previous = 'none';
		//start json construction
		//static
		var json = '{"genomebuild":"' + genome + '"'
		JSON_OBJECT['genomebuild'] = genome;
		if (matepair == "yes") {
			JSON_OBJECT['spaired'] = 'paired';
			previous = 'spaired';
		}else{
			JSON_OBJECT['spaired'] = 'no';
		}
		if (freshrun == "Fresh") {
			JSON_OBJECT['resume'] = 'no';
		}else{
			JSON_OBJECT['resume'] = 'resume';
			previous = 'resume';
		}
		JSON_OBJECT['fastqc'] = fastqc;
	
		JSON_OBJECT['barcodes'] = 'none';
		
		//submission check
		JSON_OBJECT['submission'] = submission;
		
		//adapter
		if (doAdapter == "yes") {
			previous = 'adapter';
			JSON_OBJECT['adapter'] = adapter[0].toUpperCase().replace(/\r\n|\r|\n/g, "__cr____cn__").replace(/U/g, 'T');
		}else{
			JSON_OBJECT['adapter'] = 'none';
		}
		//quality
		var JSON_ARRAY_QUALITY = {};
		if (doQuality == "yes") {
			JSON_ARRAY_QUALITY['windowSize'] = quality[0];
			JSON_ARRAY_QUALITY['requiredQuality'] = quality[1];
			JSON_ARRAY_QUALITY['leading'] = quality[2];
			JSON_ARRAY_QUALITY['trailing'] = quality[3];
			JSON_ARRAY_QUALITY['minlen'] = quality[4];
			JSON_OBJECT['quality'] = [JSON_ARRAY_QUALITY];
			previous = 'quality';
		}else{
			JSON_OBJECT['quality'] = 'none';
		}
		//trim
		var JSON_ARRAY_TRIMMING = {};
		if (doTrimming == "yes") {
			JSON_ARRAY_TRIMMING['5len1'] = trimming[1];
			JSON_ARRAY_TRIMMING['3len1'] = trimming[2];
			previous = 'trim';
		}else{
			JSON_OBJECT['trim'] = 'none';
		}
		if (trimming[0] == 'paired-end' && doTrimming == 'yes') {
			JSON_ARRAY_TRIMMING['5len2'] = trimming[3];
			JSON_ARRAY_TRIMMING['3len2'] = trimming[4];
			JSON_ARRAY_TRIMMING['trimpaired'] = 'paired';
		}
		if (doTrimming == "yes") {
			JSON_OBJECT['trim'] = [JSON_ARRAY_TRIMMING];
		}
		//split
		if (doSplit == "yes") {
			previous = 'split';
		}
		JSON_OBJECT['split'] = split[0]
	
		//expanding multiple queries
		if (doRNA == "yes"){
			var rna_string = "";
			var rnacheck = true;
			for (var i = 0; i < rna.length; i++) {
				if (rnacheck) {
					rna_string = rna[i];
					previous = rna[i];
					rnacheck = false;
				}else if (rna[i] != undefined && rnaList.indexOf(rna[i]) == -1){
					JSON_OBJECT['advparams'] = rna[i];
				}else if (rna[i] != undefined) {
					rna_string = rna_string + ',' + rna[i]
					previous = rna[i];
				}
			}
			JSON_OBJECT['commonind'] = rna_string;
		}else{
			JSON_OBJECT['commonind'] = 'none';
		}
		var customSeqSetCheck = findCustomSequenceSets(previous);
		var customSeqSet = customSeqSetCheck[0];
		if (customSeqSet.length > 0) {
			JSON_OBJECT['custominds'] = customSeqSet;
		}
		if (pipelines.length > 0) {
			JSON_OBJECT['pipeline'] = pipelines;
		}
		console.log(JSON_OBJECT);
		console.log(JSON.stringify(JSON_OBJECT));
		//end JSON construction
		
		//	Directory Checks
		var dir_check_1;
		$.ajax({
			type: 	'GET',
			url: 	BASE_PATH+'/public/api/service.php?func=checkPermissions&username='+username.clusteruser,
			async:	false,
			success: function(s)
			{
				console.log(s);
				dir_check_1 = JSON.parse(s);
			}
		});
		var dir_check_2;
		$.ajax({
			type: 	'GET',
			url: 	BASE_PATH+'/public/api/service.php?func=checkPermissions&username='+username.clusteruser+'&outdir=' + outputdir,
			async:	false,
			success: function(s)
			{
				console.log(s);
				dir_check_2 = JSON.parse(s);
			}
		});
		var dir_tests;
		if (dir_check_1.Result != 'Ok' || dir_check_2.Result != 'Ok') {
			//	perms errors
			dir_tests = false;
		}else{
			//	No errors
			dir_tests = true;
		}
	
		if (adapterCheck && doAdapter == 'yes') {
			$('#errorModal').modal({
				show: true
			});
			document.getElementById('errorLabel').innerHTML ='Please use A,T,C,G only in the adapter';
			document.getElementById('errorAreas').innerHTML = '';
		}else if (!dir_tests){
			$('#errorModal').modal({
				show: true
			});
			document.getElementById('errorLabel').innerHTML ='You do not have permissions for the directory:<br><br>'+outputdir+'<br><br>Or you do not have cluster permissions.' +
				'<br>Please visit <a href="http://umassmed.edu/biocore/resources/galaxy-group/">this website</a> for more help.';
			document.getElementById('errorAreas').innerHTML = '';
		}else if(customSeqSetCheck[1].indexOf(true) > -1){
			var custom_error = "";
			for(var k = 0; k < customSeqSetCheck[1].length; k++){
				if (customSeqSetCheck[1][k] == true) {
					custom_error += customSeqSetCheck[0][k].FullPath+'/'+customSeqSetCheck[0][k].IndexPrefix+'<br><br>';
				}
			}
			$('#errorModal').modal({
				show: true
			});
			document.getElementById('errorLabel').innerHTML ='You do not have permissions or the file does not exist for the File:<br><br>'+custom_error
			document.getElementById('errorAreas').innerHTML = '';
		}else{
			//insert new values into ngs_runparams
			var runparamsInsert = postInsertRunparams(JSON_OBJECT, outputdir, run_name, description, perms, group);
			//insert new values into ngs_runlist
			console.log(runparamsInsert);
			console.log(ids);
			var submitted = postInsertRunlist(runparamsInsert[0], ids, runparamsInsert[1]);
			if (submitted) {
				window.location.href = BASE_PATH+"/stat/status";
			}
		}
	}
}

function commonRNACheck(id){
	console.log(id);
}

function sendToFastlane(){
	window.location.href = BASE_PATH+"/fastlane";
}

function sendToStatus(){
	window.location.href = BASE_PATH+"/stat/status/";
}

function generateTableLink(){
	window.location.href = BASE_PATH+"/tablecreator";
}

function backFromDetails(back_type){
	var changeHTML = '';
	var hrefSplit = window.location.href.split("/");
	var search = hrefSplit[hrefSplit.length - 1];
	var id = hrefSplit[hrefSplit.length - 2];
	var type = hrefSplit[hrefSplit.length - 3];

	if (back_type == 'Sample') {
	hrefSplit[hrefSplit.length - 2] = getLaneIdFromSample(hrefSplit[hrefSplit.length - 2]);
	hrefSplit[hrefSplit.length - 3] = 'experiments'
	}else if (back_type == 'Experiment') {
	hrefSplit[hrefSplit.length - 2] = getSeriesIdFromLane(hrefSplit[hrefSplit.length - 2]);
	hrefSplit[hrefSplit.length - 3] = 'experiment_series'
	}else{
		searchSplit = hrefSplit[hrefSplit.length - 1].split('$');
		lastSearch = searchSplit[searchSplit.length - 1].split('=');

		if (lastSearch[1] != undefined) {
		hrefSplit[hrefSplit.length - 2] = lastSearch[1];
		hrefSplit[hrefSplit.length - 3] = lastSearch[0];
		hrefSplit[hrefSplit.length - 4] = 'browse';
		}else{
		hrefSplit.pop();
		hrefSplit.pop();
		hrefSplit.pop();
		hrefSplit[hrefSplit.length -1] = 'index';
		}
	}

	for (var x = 0; x < hrefSplit.length; x++) {
	if (x == (hrefSplit.length - 1)) {
		changeHTML += hrefSplit[x];
	}else{
		changeHTML += hrefSplit[x] + '/';
	}
	}
	window.location.href = changeHTML;
}

/*##### CHECKBOX FUNCTIONS #####*/
function manageChecklists(name, type){
	var lane_check;
	var experiment_check;
	name = parseInt(name);
	if (type == 'sample_checkbox') {
		//sample checkbox
		var sample_search = searchIDDictionary(name);
		if (sample_search == undefined) {
			//	No sample found to be displayed
			return;
		}else{
			lane_check = sample_search[0];
			experiment_check = sample_search[1];
			var lane_samples = seachLaneToSamples(lane_check);
			var lanes_bool = true;
			var experiment_samples = searchExperimentToSamples(experiment_check);
			var experiment_bool = true;
		}
		if ( checklist_samples.indexOf( name ) > -1 ){
			//remove
			checklist_samples.splice(checklist_samples.indexOf(name), 1);
			removeFromDolphinBasket(name);
			if (checklist_lanes.indexOf(parseInt(lane_check)) > -1) {
				for(var x = 0; x < lane_samples.length; x++){
					if (lane_samples[x] == undefined) {
						lanes_bool = false;
					}else{
						if (checklist_samples.indexOf(lane_samples[x]) > -1 && lanes_bool) {
							lanes_bool = false;
						}
					}
				}
				if (lanes_bool) {
					if (document.getElementById('lane_checkbox_' + lane_check) != undefined) {
						var check = document.getElementById('lane_checkbox_' + lane_check);
						check.checked = !check.checked;
					}
					checklist_lanes.splice(checklist_lanes.indexOf(parseInt(lane_check)), 1);
				}
			}
			if (checklist_experiment_series.indexOf(parseInt(experiment_check)) > -1) {
				for(var x = 0; x < experiment_samples.length; x++){
					if (experiment_samples[x] == undefined) {
						experiment_bool = false;
					}else{
						if (checklist_samples.indexOf(experiment_samples[x]) > -1 && experiment_bool) {
							experiment_bool = false;
						}
					}
				}
				if (experiment_bool) {
					if (document.getElementById('experiment_checkbox_' + experiment_check) != undefined) {
						var check = document.getElementById('experiment_checkbox_' + experiment_check);
						check.checked = !check.checked;
					}
					checklist_experiment_series.splice(checklist_experiment_series.indexOf(parseInt(experiment_check)), 1);
				}
			}
			if (document.getElementById('sample_checkbox_' + name) != undefined) {
				if (document.getElementById('sample_checkbox_' + name).checked != false) {
					var check = document.getElementById('sample_checkbox_' + name);
					check.checked = !check.checked;
				}
			}
			if (checklist_samples.length == 0) {
				document.getElementById('clear_basket').disabled = 'true';
			}
		}else{
			//add
			checklist_samples.push(name);
			addToDolphinBasket(name);
			sendBasketInfo(name);
			
			if (document.getElementById('clear_basket').disabled) {
				document.getElementById('clear_basket').disabled = false;
			}
			if (document.getElementById('sample_checkbox_' + name) != undefined) {
				if (document.getElementById('sample_checkbox_' + name).checked != true) {
					var check = document.getElementById('sample_checkbox_' + name);
					check.checked = !check.checked;
				}
			}
			if (checklist_lanes.indexOf(parseInt(lane_check)) == -1) {
				for(var x = 0; x < lane_samples.length; x++){
					if (lane_samples[x] == undefined) {
						lanes_bool = false;
					}else{
						if (checklist_samples.indexOf(lane_samples[x]) == -1 && lanes_bool) {
							lanes_bool = false;
						}
					}
				}
				if (lanes_bool) {
					if (document.getElementById('lane_checkbox_' + lane_check) != undefined) {
						var check = document.getElementById('lane_checkbox_' + lane_check);
						check.checked = !check.checked;
					}
					checklist_lanes.push(parseInt(lane_check));
				}
			}
			
			if (checklist_experiment_series.indexOf(parseInt(experiment_check)) == -1) {
				for(var x = 0; x < experiment_samples.length; x++){
					if (experiment_samples[x] == undefined) {
						experiment_bool = false;
					}else{
						if (checklist_samples.indexOf(experiment_samples[x]) == -1 && experiment_bool) {
							experiment_bool = false;
						}
					}
				}
				if (experiment_bool) {
					if (document.getElementById('experiment_checkbox_' + experiment_check) != undefined) {
						var check = document.getElementById('experiment_checkbox_' + experiment_check);
						check.checked = !check.checked;
					}
					checklist_experiment_series.push(parseInt(experiment_check));
				}
			}
		}
	}else if (type == "experiment_checkbox") {
		//	experiment series checkbox
		if (checklist_experiment_series.indexOf(name) > -1) {
			//remove
			checklist_experiment_series.splice(checklist_experiment_series.indexOf(name), 1);
			var experiment_samples = searchExperimentToSamples(name);
			for (var y = 0; y < experiment_samples.length; y++){
				if ( checklist_samples.indexOf( experiment_samples[y] ) > -1 ){
					manageChecklists(experiment_samples[y], 'sample_checkbox');
				}
			}
		}else{
			//add
			checklist_experiment_series.push(name);
			var experiment_samples = searchExperimentToSamples(name);
			for (var y = 0; y < experiment_samples.length; y++){
				if ( checklist_samples.indexOf( experiment_samples[y] ) < 0 ){
					manageChecklists(experiment_samples[y], 'sample_checkbox');
				}
			}
		}
	}else{
		//lane checkbox
		if ( checklist_lanes.indexOf( name ) > -1 ){
			//remove
			checklist_lanes.splice(checklist_lanes.indexOf(name), 1);
			var lane_samples = seachLaneToSamples(name);
			for (var x = 0; x < lane_samples.length; x++) {
				if ( checklist_samples.indexOf( lane_samples[x] ) > -1 ){
					manageChecklists(lane_samples[x], 'sample_checkbox');
				}
			}
		}
		else
		{
			//add
			checklist_lanes.push(name);
			var lane_samples = seachLaneToSamples(name);
			for (var x = 0; x < lane_samples.length; x++) {
				if (checklist_samples.indexOf(lane_samples[x]) < 0) {
					manageChecklists(lane_samples[x], 'sample_checkbox');
				}
			}
		}
	}
}

//	Store all ids in tree based JSON
function generateIDDictionary(experiment_series_data, lane_data, sample_data){
	STORED_SAMPLE_DATA = sample_data;
	//	for each es
	for(var x = 0; x < experiment_series_data.length; x++){
		temp_lane_dict = [];
		//	for each lane
		for(var y = 0; y < lane_data.length; y++){
			//	if lane is in es
			if (lane_data[y].series_id == experiment_series_data[x].id) {
				var temp_sample_dict = [];
				//	for each sample in lane
				for(var z = 0; z < sample_data.length; z++){
					//	if sample in lane
					if (sample_data[z].lane_id == lane_data[y].id) {
						//	push to sample var
						temp_sample_dict.push(sample_data[z].id);
					}
				}
				//	store in temp lane var
				temp_lane_dict.push(JSON.parse('{"'+[lane_data[y].id]+"\":["+temp_sample_dict.toString()+"]}"));
			}
		}
		//	store lane/sample side into dictionary under es
		ID_DICTIONARY[experiment_series_data[x].id] = temp_lane_dict;
	}
	console.log(ID_DICTIONARY);
}

//	Search JSON dictionary for lane and es id
function searchIDDictionary(sample){
	//	Grab dictionary key variables
	var es_keys = Object.keys(ID_DICTIONARY);
	//	For each es key
	for(var es_key in es_keys){
		//	For each es, find position of lane id
		for(var x = 0; x < ID_DICTIONARY[es_keys[es_key]].length; x++){
			//	Grab lane ids
			var lane_keys = Object.keys(ID_DICTIONARY[es_keys[es_key]][x]);
			//	For each lane id
			for(lane_key in lane_keys){
				//	if lane array contains sample
				if (ID_DICTIONARY[es_keys[es_key]][x][lane_keys[lane_key]].indexOf(parseInt(sample)) > -1) {
					//	Return ids
					return [lane_keys[lane_key], es_keys[es_key]];
				}
			}
		}
	}
	return undefined;
}

//	Search JSON dictionary for samples for a given lane id
function seachLaneToSamples(lane){
	//	Grab dictionary key variables
	var es_keys = Object.keys(ID_DICTIONARY);
	//	For each es key
	for(var es_key in es_keys){
		//	For each es, find position of lane id
		for(var x = 0; x < ID_DICTIONARY[es_keys[es_key]].length; x++){
			//	Grab lane ids
			var lane_keys = Object.keys(ID_DICTIONARY[es_keys[es_key]][x]);
			//	For each lane id
			if (ID_DICTIONARY[es_keys[es_key]][x][lane] != undefined) {
				//	Return ids
				return ID_DICTIONARY[es_keys[es_key]][x][lane];
			}
		}
	}
	return [];
}

function searchExperimentToSamples(experiment){
	var combined_samples = [];
	//	For the lanes within the es
	for(var x = 0; x < ID_DICTIONARY[experiment].length; x++){
		//	grab each lane id
		var lane_keys = Object.keys(ID_DICTIONARY[experiment][x]);
		//	for each id
		for(var lane_key in lane_keys){
			for(var y = 0; y < ID_DICTIONARY[experiment][x][lane_keys[lane_key]].length; y++){
				combined_samples.push(ID_DICTIONARY[experiment][x][lane_keys[lane_key]][y]);
			}
		}
	}
	return combined_samples;
}

function reloadBasket(){
	var lastBasket = getBasketInfo();
	if (lastBasket != undefined) {
		var basketArray = lastBasket.split(",");
		console.log(basketArray);
		for (var x = 0; x < basketArray.length; x++) {
			if (basketArray != '0') {
				manageChecklists(basketArray[x], 'sample_checkbox');
			}
		}
	}
}

function addToDolphinBasket(sampleID){
	var sample_info = storedSampleSearch(sampleID);
	var table = $('#dolphin_basket').dataTable();
	
	if (table != null) {
		table.fnAddData([
			sampleID,
			sample_info.samplename,
			'<button id="remove_basket_'+sampleID+'" class="btn btn-danger btn-xs pull-right" onclick="manageChecklists(\''+sampleID+'\', \'sample_checkbox\')"><i class="fa fa-times"></i></button>'
		])
	}
}

function storedSampleSearch(sample){
	for(var x = 0; x < STORED_SAMPLE_DATA.length; x++){
		if (STORED_SAMPLE_DATA[x].id == sample) {
			return STORED_SAMPLE_DATA[x];
		}
	}
	return [];
}

function removeFromDolphinBasket(sampleID){
	var table = $('#dolphin_basket').dataTable();
	var data = table.fnGetData();
	for (var samp_data in data) {
		if (data[samp_data][0] == sampleID) {
			data.splice(samp_data, 1);
		}
	}
	table.fnClearTable();
	if (data.toString() != "") {
		table.fnAddData(data);
	}else{
		table.fnClearTable();
	}
	removeBasketInfo(sampleID);
}

function clearBasket(){
	for(var x = (checklist_samples.length - 1); x >= 0; x = x - 1){
		manageChecklists(checklist_samples[x], 'sample_checkbox');
	}
	flushBasketInfo();
}

function checkOffAllSamples(){
	var hrefSplit = window.location.href.split("/");
	var searchLoc = $.inArray('search', hrefSplit);
	var samplesCheck = $.inArray('samples', hrefSplit);

	if (searchLoc != -1 && samplesCheck == -1) {
		var pagination = document.getElementById('st_pagination_samples');
		var pagination_ul = pagination.childNodes;
		var pagination_li = pagination_ul[0].childNodes;
		for(var y = 0; y < pagination_li.length; y++){
			pagination_li[y].setAttribute('onClick', 'checkCheckedList()');
		}
	}
}

function checkOffAllLanes(){
	var hrefSplit = window.location.href.split("/");
	var searchLoc = $.inArray('search', hrefSplit);
	var experimentsCheck = $.inArray('experiments', hrefSplit);
	var samplesCheck = $.inArray('samples', hrefSplit);
	

	if (searchLoc != -1 && experimentsCheck == -1 && samplesCheck == -1) {
		var pagination = document.getElementById('st_pagination_lanes');
		var pagination_ul = pagination.childNodes;
		pagination_ul[0].setAttribute('onClick', 'checkCheckedLanes()');
		var pagination_li = pagination_ul[0].childNodes;
		for(var y = 0; y < pagination_li.length; y++){
			pagination_li[y].setAttribute('onClick', 'checkCheckedLanes()');
		}
	}
}

function getValidSamples(lane_samples){
	var valid_samples;
	$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/initialmappingdb.php",
				data: { p: 'laneToSampleChecking', sample_ids: lane_samples.toString()},
				async: false,
				success : function(r)
				{
					valid_samples = r;
				}
			});
	return valid_samples;
}

function checkCheckedList(){
	var allSamples = getAllSampleIds();
	console.log(allSamples);
	for (var x = 0; x < allSamples.length; x++){
		if ( document.getElementById('sample_checkbox_' + allSamples[x]) != null) {
			if (checklist_samples.indexOf(parseInt(allSamples[x])) > -1 || checklist_samples.indexOf(allSamples[x]) > -1) {
				document.getElementById('sample_checkbox_' + allSamples[x]).setAttribute('checked', 'true');
			}else{
				document.getElementById('sample_checkbox_' + allSamples[x]).removeAttribute('checked');
			}
		}
	}
	
}

function checkCheckedLanes(){
	var allLanes = getAllLaneIds();
	for (var x = 0; x < allLanes.length; x++){
		if ( document.getElementById('lane_checkbox_' + allLanes[x]) != null) {
			if (checklist_lanes.indexOf(parseInt(allLanes[x])) > -1 || checklist_lanes.indexOf(allLanes[x]) > -1) {
				document.getElementById('lane_checkbox_' + allLanes[x]).setAttribute('checked', 'true');
			}else{
				document.getElementById('lane_checkbox_' + allLanes[x]).removeAttribute('checked');
			}
		}
	}
}

function checkCheckedExperiments(){
	var allExperiments = getAllExperimentIds();
	for (var x = 0; x < allExperiments.length; x++){
		if ( document.getElementById('experiment_checkbox_' + allExperiments[x]) != null) {
			if (checklist_experiment_series.indexOf(parseInt(allExperiments[x])) > -1 || checklist_experiment_series.indexOf(allExperiments[x]) > -1) {
				document.getElementById('experiment_checkbox_' + allExperiments[x]).setAttribute('checked', 'true');
			}else{
				document.getElementById('experiment_checkbox_' + allExperiments[x]).removeAttribute('checked');
			}
		}
	}
}

function passIDData(run_group_id, id){
	currentChecked = name;
	//lane checkbox
	if ( checklist_lanes.indexOf( id ) > -1 ){
	//remove
	checklist_lanes.pop(id);
	checklist_samples.pop(run_group_id);
	}else{
	//add
	checklist_lanes.push(id);
	checklist_samples.push(run_group_id);
	}
}

function returnToIndex(){
	window.location.href = BASE_PATH+"/search/index/";
}

function changeDataGroup(command){
	if (checklist_experiment_series.length == 1) {
		var experiment_series_group;
		$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/ngsquerydb.php",
			data: { p: 'getExperimentSeriesGroup', experiment: checklist_experiment_series.toString() },
			async: false,
			success : function(s)
			{
				experiment_series_group = s;
			}
		});
		if (command == 'change_owner') {
			//	Change Experiment Series Owner
			document.getElementById('permsLabel').innerHTML = 'Which user should own the selected Experiment Series?';
			document.getElementById('permsDiv').innerHTML = '<select id="permsIDSelect" class="form-control"></select>';
			$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/ngsquerydb.php",
				data: { p: 'getAllUsers', experiment: checklist_experiment_series.toString() },
				async: false,
				success : function(s)
				{
					console.log(s);
					for(var x = 0; x < s.length; x++){
						if (s[x].id == experiment_series_group[0].owner_id) {
							document.getElementById('permsIDSelect').innerHTML += '<option value="' + s[x].id + '" selected="true">' + s[x].username + '</option>';
						}else{
							document.getElementById('permsIDSelect').innerHTML += '<option value="' + s[x].id + '">' + s[x].username + '</option>';
						}
					}
				}
			});
			if (document.getElementById('permsIDSelect').innerHTML != '') {
				document.getElementById('confirmPermsButton').setAttribute('style', 'display:show');
				document.getElementById('cancelPermsButton').innerHTML = 'Cancel';
				document.getElementById('confirmPermsButton').setAttribute('onclick', 'confirmOwnerPressed()');
			}else{
				document.getElementById('permsLabel').innerHTML = 'You do not have permissions to change this Experiment Series Group.';
				document.getElementById('permsDiv').innerHTML = '';
				document.getElementById('confirmPermsButton').setAttribute('style', 'display:none');
				document.getElementById('cancelPermsButton').innerHTML = 'OK';
			}
		}else if (command == 'change_group') {
			//	Change Experiment Series group
			document.getElementById('permsLabel').innerHTML = 'Which group should own the selected Experiment Series?';
			document.getElementById('permsDiv').innerHTML = '<select id="permsIDSelect" class="form-control"></select>';
			$.ajax({ type: "GET",
					url: BASE_PATH+"/public/ajax/ngsquerydb.php",
					data: { p: 'changeDataGroupNames', experiment: checklist_experiment_series.toString() },
					async: false,
					success : function(s)
					{
						console.log(s);
						for(var x = 0; x < s.length; x++){
							if (s[x].id == experiment_series_group[0].group_id) {
								document.getElementById('permsIDSelect').innerHTML += '<option value="' + s[x].id + '" selected="true">' + s[x].name + '</option>';
							}else{
								document.getElementById('permsIDSelect').innerHTML += '<option value="' + s[x].id + '">' + s[x].name + '</option>';
							}
						}
					}
				});
			if (document.getElementById('permsIDSelect').innerHTML != '') {
				document.getElementById('confirmPermsButton').setAttribute('style', 'display:show');
				document.getElementById('cancelPermsButton').innerHTML = 'Cancel';
				document.getElementById('confirmPermsButton').setAttribute('onclick', 'confirmPermsPressed()');
			}else{
				document.getElementById('permsLabel').innerHTML = 'You do not have permissions to change this Experiment Series Group.';
				document.getElementById('permsDiv').innerHTML = '';
				document.getElementById('confirmPermsButton').setAttribute('style', 'display:none');
				document.getElementById('cancelPermsButton').innerHTML = 'OK';
			}
		}
	}else if (checklist_experiment_series.length == 0){
		document.getElementById('permsLabel').innerHTML = 'You must select a Experiment Series to change it\'s group.';
		document.getElementById('permsDiv').innerHTML = '';
		document.getElementById('confirmPermsButton').setAttribute('style', 'display:none');
		document.getElementById('cancelPermsButton').innerHTML = 'OK';
	}else{
		document.getElementById('permsLabel').innerHTML = 'You may only select one Experiment Series at a time.';
		document.getElementById('permsDiv').innerHTML = '';
		document.getElementById('confirmPermsButton').setAttribute('style', 'display:none');
		document.getElementById('cancelPermsButton').innerHTML = 'OK';
	}
	$('#permsModal').modal({
		show: true
	});
}

function confirmPermsPressed(){
	console.log(document.querySelector("select").selectedOptions[0].value);
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngsquerydb.php",
		data: { p: 'changeDataGroup', group_id: document.querySelector("select").selectedOptions[0].value, experiment: checklist_experiment_series.toString() },
		async: false,
		success : function(s)
		{
			console.log(s);
		}
	});
	document.getElementById('permsLabel').innerHTML = 'Selected data\'s group has been changed!'
	document.getElementById('permsDiv').innerHTML = '';
	document.getElementById('confirmPermsButton').setAttribute('style', 'display:none');
	document.getElementById('cancelPermsButton').innerHTML = 'OK';
}

function confirmOwnerPressed(){
	console.log(document.querySelector("select").selectedOptions[0].value);
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngsquerydb.php",
		data: { p: 'changeOwnerExperiment', owner_id: document.querySelector("select").selectedOptions[0].value, experiment: checklist_experiment_series.toString() },
		async: false,
		success : function(s)
		{
			console.log(s);
		}
	});
	document.getElementById('permsLabel').innerHTML = 'Selected data\'s owner has been changed!'
	document.getElementById('permsDiv').innerHTML = '';
	document.getElementById('confirmPermsButton').setAttribute('style', 'display:none');
	document.getElementById('cancelPermsButton').innerHTML = 'OK';
}

/*##### SEND TO PIPELINE WITH SELECTION #####*/
function submitSelected(){
	if (selectionTest()) {
		if (initialRunTest()) {
			window.location.href = BASE_PATH+"/pipeline/selected/" + checklist_samples + "$";
		}
	}
}

function selectionTest(){
	if (checklist_samples.length > 0) {
		return true;
	}else{
		$('#deleteModal').modal({
			show: true
		});
		document.getElementById('myModalLabel').innerHTML = 'Selection error';
		document.getElementById('deleteLabel').innerHTML ='No samples/imports selected.  Please select samples/imports in order to send them to the pipeline';
		document.getElementById('deleteAreas').innerHTML = '';
			
		document.getElementById('cancelDeleteButton').innerHTML = "OK";
		document.getElementById('confirmDeleteButton').setAttribute('style', 'display:none');
		return false;
	}
}

function initialRunTest(){
	var valid_samples = [];
	$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/browse_edit.php",
				data: { p: 'intialRunCheck', samples: checklist_samples.toString()},
				async: false,
				success : function(r)
				{
					console.log(r);
					for (var x = 0; x < r.length; x++) {
						valid_samples.push(r[x].sample_id);
					}
				}
			});
	if (valid_samples.length == checklist_samples.length) {
		return true;
	}else{
		$('#deleteModal').modal({
			show: true
		});
		
		var spliced_samples = checklist_samples.slice(0);
		for(var x = 0; x < valid_samples.length; x++){
			var loc = spliced_samples.indexOf(parseInt(valid_samples[x]));
			spliced_samples.splice(loc, 1);
		}
		
		document.getElementById('myModalLabel').innerHTML = 'Selection error';
		document.getElementById('deleteLabel').innerHTML ='Some samples/imports selected have not finished their initial processing.';
		document.getElementById('deleteAreas').innerHTML = 'You cannot use these sample(s) within the pipeline until they finish their initial processing:' +
			'<br><br>Sample id(s): ' + spliced_samples.join(", ");
			
		document.getElementById('cancelDeleteButton').innerHTML = "OK";
		document.getElementById('confirmDeleteButton').setAttribute('style', 'display:none');
		return false;
	}
}

/*##### CHECK RADIO SELECTED FUNCTION #####*/
function findRadioChecked(title){
	var value = ""
	if (document.getElementById(title+"_yes").checked) {
		value = document.getElementById(title+"_yes").value;
	}else{
		value = document.getElementById(title+"_no").value;
	}
	return value;
}

function findRNAChecked(titles){
	var value = ""
	for(var x = 0; x < titles.length - 1; x++){
		if (document.getElementById(titles[x]+"_yes").checked) {
			value = document.getElementById(titles[x]+"_yes").value;
		}
	}
	if (value == "") {
		value = "no"
	}
	return value;
}

/*##### REMOVE PIPELINES #####*/
function removePipes(num){
	var div = document.getElementById('TESTBOXAREA_'+ num);
	var index = currentPipelineID.indexOf(num);
	div.parentNode.removeChild(div);
	if (currentPipelineVal[index] == pipelineDict[0]) {
		rsemSwitch = false;
	}
	if (index != -1) {
		currentPipelineVal.splice(index,1);
		currentPipelineID.splice(index,1);
	}
}

/*##### ADD PIPELINES #####*/
function additionalPipes(){
	//find parent div
	var master = document.getElementById('masterPipeline');
	//create children divs/elements
	var outerDiv = createElement('div', ['id', 'class', 'style'], ['TESTBOXAREA_'+pipelineNum, 'callout callout-info margin', 'display:""']);
	var innerDiv = document.createElement( 'div' );
	//attach children to parent
	innerDiv.appendChild( createElement('select',
					['id', 'class', 'onchange', 'OPTION_DIS_SEL', 'OPTION', 'OPTION', 'OPTION', 'OPTION', 'OPTION'],
					['select_'+pipelineNum, 'form-control', 'pipelineSelect('+pipelineNum+')', '--- Select a Pipeline ---',
					pipelineDict[0], pipelineDict[1], pipelineDict[2], pipelineDict[3], pipelineDict[4]]));
	innerDiv.appendChild( createElement('div', ['id'], ['select_child_'+pipelineNum]));
	outerDiv.appendChild( innerDiv );
	outerDiv.appendChild( createElement('input', ['id', 'type', 'class', 'style', 'value', 'onclick'],
					['removePipe_'+pipelineNum, 'button', 'btn btn-primary', 'display:""', 'Remove Pipeline',
					'removePipes('+pipelineNum+')']));
	//attach to master
	master.appendChild( outerDiv );
	pipelineNum = pipelineNum + 1;
}

function deselectCondition(condition, pipeNum){
	var selection = document.getElementById('multi_select_'+condition+'_'+pipeNum);
	if (condition == 1) {
		var opposite = 2;
	}else if(condition == 2){
		var opposite = 1;
	}else if (condition == 3) {
		var opposite = 4;
	}else if (condition == 4) {
		var opposite = 3;
	}
	var opposite_selection = document.getElementById('multi_select_'+opposite+'_'+pipeNum);
	for (var x = 0; x < selection.options.length; x++) {
		var opt = selection.options[x];
		var opposite_opt = opposite_selection.options[x];
		if (opt.selected) {
			opposite_opt.disabled = true;
			opposite_opt.style.opacity = 0.4;
		}else{
			opposite_opt.disabled = false;
			opposite_opt.style.opacity = 1;
		}
	}
}

function getMultipleSelectionBox(selection) {
	var selected = [];
	for (var x = 0; x < selection.options.length; x++) {
		var opt = selection.options[x];
		if (opt.selected) {
			selected.push(opt.value);
		}
	}
	return selected;
}

 /*##### PUSH IF SELECTED FUNCTION #####*/
function findAdditionalInfoValues(goWord, additionalArray){
	var values = [];
	if (goWord == "yes") {
	for (var i = 0, len = additionalArray.length; i < len; i++) {
		if (document.getElementById(additionalArray[i]+'_val') != null) {
		values.push(document.getElementById(additionalArray[i]+'_val').value);
		}else{
		if(document.getElementById(additionalArray[i]+'_yes') != null){
			if(document.getElementById(additionalArray[i]+'_yes').checked){
			values.push(additionalArray[i]);
			}
		}
		}
	}
	}else{
	for (var i = 0, len = additionalArray.length; i < len; i++) {
		values.push('none');
	}
	}
	return values;
}
/*##### GENERATE ADDITIONAL PIPELINE STR FOR JSON #####*/
function findPipelineValues(){
	var RSEM_JSON_DICT  = ['Params', 'IGVTDF', 'BAM2BW', 'ExtFactor',  'RSeQC'];
	var DESEQ_JSON_DICT = ['Name', 'Columns', 'Conditions', 'FitType', 'HeatMap', 'padj', 'foldChange', 'DataType'];
	var CHIPSEQ_JSON_DICT = ['ChipInput', 'MultiMapper', 'TagSize', 'BandWith', 'EffectiveGenome', 'IGVTDF', 'BAM2BW', 'ExtFactor', 'CollectMultipleMetrics', 'MarkDuplicates'];
	var TOPHAT_JSON_DICT = ['Params', 'IGVTDF', 'BAM2BW', 'ExtFactor', 'RSeQC', 'CollectRnaSeqMetrics', 'CollectMultipleMetrics', 'MarkDuplicates'];
	var BISULPHITE_JSON_DICT = ['BSMapStep', 'BisulphiteType', 'Digestion', 'BSMapParams', 'IGVTDF', 'BAM2BW', 'ExtFactor', 'CollectMultipleMetrics', 'MarkDuplicates', 'MCallStep', 'Columns', 'Conditions', 'MCallParams', 'MCompStep', 'MCompParams'];
	
	var JSON_ARRAY =  [];
	for (var y = 0; y < currentPipelineID.length; y++) {
		var JSON_OBJECT = {};
		var USED_DICT;
		if (currentPipelineVal[y] == 'RNASeqRSEM') {
			USED_DICT = RSEM_JSON_DICT;
		}else if (currentPipelineVal[y] == 'DESeq') {
			USED_DICT = DESEQ_JSON_DICT;
		}else if (currentPipelineVal[y] == 'ChipSeq') {
			USED_DICT = CHIPSEQ_JSON_DICT;
		}else if (currentPipelineVal[y] == 'Tophat') {
			USED_DICT = TOPHAT_JSON_DICT;
		}else if (currentPipelineVal[y] == 'BisulphiteMapping') {
			USED_DICT = BISULPHITE_JSON_DICT;
		}
		
		var dict_counter = 0;
		JSON_OBJECT['Type'] = currentPipelineVal[y];
		
		var masterDiv = document.getElementById('select_child_'+currentPipelineID[y]).getElementsByTagName('*');
		var conditions_array = [];
		var conditions_type_array = [];
		var multireset = false;
		for (var x = 0; x < masterDiv.length; x++) {
			if (multireset == true) {
				conditions_array = [];
				conditions_type_array = [];
				multireset = false;
			}
			var e = masterDiv[x];
			if (e.type != undefined) {
				if (e.type == "select-multiple") {
					if (conditions_array.length == 0) {
						var current_cond = getMultipleSelectionBox(e);
						for(z in current_cond){
							conditions_array.push(current_cond[z]);
							conditions_type_array.push('Cond1');
						}
					}else{
						var current_cond2 = getMultipleSelectionBox(e);
						for(z in current_cond2){
							conditions_array.push(current_cond2[z]);
							conditions_type_array.push('Cond2');
						}
						if (conditions_array.length > 0) {
							JSON_OBJECT[USED_DICT[dict_counter]] = conditions_type_array.toString();
							JSON_OBJECT[USED_DICT[dict_counter - 1]] = conditions_array.toString();
							multireset = true;
						}
					}
				}else{
					if (e.type == 'checkbox') {
						if (e.checked) {
							JSON_OBJECT[USED_DICT[dict_counter]] = 'yes';
						}else{
							JSON_OBJECT[USED_DICT[dict_counter]] = 'no';
						}
					}else if(e.type != 'radio'){
						JSON_OBJECT[USED_DICT[dict_counter]] = e.value.replace(/\r\n|\r|\n/g, "__cr____cn__");
					}else{
						if (e.checked) {
							JSON_OBJECT[USED_DICT[dict_counter]] = e.value;
						}else{
							dict_counter--;
						}
					}
				}
				dict_counter++;
			}
		}
		JSON_ARRAY.push(JSON_OBJECT);
	}
	console.log(JSON_ARRAY);
	return JSON_ARRAY;
}

/*##### GENERATE ADDITIONAL CUSTOM SEQUENCE SET FOR JSON #####*/
function findCustomSequenceSets(previous){
	var CUSTOM_SEQ_DICT = ['FullPath', 'IndexPrefix', 'BowtieParams', 'Description', 'Filter Out'];
	var JSON_ARRAY =  [];
	var file_check_array = [];
	for (var y = 0; y < customSeqNumCheck.length; y++){
		var JSON_OBJECT = {};
		var dict_counter = 0;
		var full_path = "";
		var masterDiv = document.getElementById('custom_seq_inner_'+customSeqNumCheck[y]).getElementsByTagName('*');
		for (var x = 0; x < masterDiv.length - 1; x++){
			var e = masterDiv[x];
			if (e.type != undefined) {
				if (x == 2) {
					full_path = e.value;
					JSON_OBJECT[CUSTOM_SEQ_DICT[dict_counter]] = e.value;
				}
				if (x == 5) {
					var file_check_1 = "";
					var file_check_2 = "";
					var file_check_results_1 = [];
					var file_check_results_2 = [];
					if (e.value.indexOf(".fasta") > -1 || e.value.indexOf(".fa") > -1) {
						var file_check_1 = e.value;
					}else{
						var file_check_1 = e.value + ".fasta";
						var file_check_2 = e.value + ".fa";
					}
					if (file_check_1 != "") {
						$.ajax({
							type: 	'GET',
							url: 	BASE_PATH+'/public/api/service.php?func=checkFile&username='+username.clusteruser+'&file=' + full_path + "/" + file_check_1,
							async:	false,
							success: function(s)
							{
								console.log(s);
								file_check_results_1 = JSON.parse(s);
								console.log(file_check_results_1);
							}
						});
					}
					if (file_check_2 != "") {
						$.ajax({
							type: 	'GET',
							url: 	BASE_PATH+'/public/api/service.php?func=checkFile&username='+username.clusteruser+'&file=' + full_path + "/" + file_check_2,
							async:	false,
							success: function(s)
							{
								console.log(s);
								file_check_results_2 = JSON.parse(s);
								console.log(file_check_results_2);
							}
						});
					}
					if (file_check_results_1.Result == "Ok") {
						file_check_array.push(false);
						var temp_file = file_check_1.split(".");
						temp_file.pop();
						JSON_OBJECT[CUSTOM_SEQ_DICT[dict_counter]] = temp_file.join(".");
					}else if (file_check_results_2.Result == "Ok") {
						file_check_array.push(false);
						JSON_OBJECT[CUSTOM_SEQ_DICT[dict_counter]] = e.value;
					}else{
						file_check_array.push(true);
						JSON_OBJECT[CUSTOM_SEQ_DICT[dict_counter]] = e.value;
					}
				}else{
					JSON_OBJECT[CUSTOM_SEQ_DICT[dict_counter]] = e.value;
				}
				dict_counter++;
			}
		}
		JSON_ARRAY.push(JSON_OBJECT);
	}
	return [JSON_ARRAY, file_check_array];
}

function sequenceSetsBtn(){
	var outerDiv = document.getElementById('custom_seq_outer');
	var innerDiv = createElement('div', ['id', 'class'], ['custom_seq_inner_'+customSeqNum, 'callout callout-info margin']);

	var babyDiv1 = createElement('div', [], []);
	var babyDiv2 = createElement('div', [], []);
	var babyDiv3 = createElement('div', [], []);
	var babyDiv4 = createElement('div', [], []);
	var babyDiv5 = createElement('div', [], []);

	babyDiv1.appendChild(createElement('label', ['class','TEXTNODE'], ['box-title', 'Custom sequence index directory (full path)']));
	babyDiv1.appendChild(createElement('input', ['id', 'class', 'type', 'value'], ['custom_1_'+customSeqNum, 'form-control', 'text', '']));
	innerDiv.appendChild(babyDiv1);

	babyDiv2.appendChild(createElement('label', ['class','TEXTNODE'], ['box-title', 'Name of the index file (prefix)']));
	babyDiv2.appendChild(createElement('input', ['id', 'class', 'type', 'value'], ['custom_2_'+customSeqNum, 'form-control', 'text', '']));
	innerDiv.appendChild(babyDiv2);

	babyDiv3.appendChild(createElement('label', ['class','TEXTNODE'], ['box-title', 'Bowtie parameters']));
	babyDiv3.appendChild(createElement('input', ['id', 'class', 'type', 'value'], ['custom_3_'+customSeqNum, 'form-control', 'text', '-N 1']));
	innerDiv.appendChild(babyDiv3);

	babyDiv4.appendChild(createElement('label', ['class','TEXTNODE'], ['box-title', 'Description']));
	babyDiv4.appendChild(createElement('input', ['id', 'class', 'type', 'value'], ['custom_4_'+customSeqNum, 'form-control', 'text', '']));
	innerDiv.appendChild(babyDiv4);

	babyDiv5.appendChild(createElement('label', ['class','TEXTNODE'], ['box-title', 'Filter Out']));
	babyDiv5.appendChild(createElement('select', ['id', 'class', 'value', 'OPTION', 'OPTION'], ['custom_5_'+customSeqNum, 'form-control', '', 'yes', 'no']));
	innerDiv.appendChild(babyDiv5);

	innerDiv.appendChild(createElement('input', ['id', 'class', 'type', 'value', 'onclick'],['remove_custom_'+customSeqNum, 'btn btn-primary', 'button', 'Remove Custom Sequence Set', 'removeSequenceSetsBtn('+customSeqNum+')']));
	outerDiv.appendChild(innerDiv);

	customSeqNumCheck.push(customSeqNum);
	customSeqNum++;
}

function removeSequenceSetsBtn(num){
	var removing = document.getElementById('custom_seq_inner_'+num);
	removing.parentNode.removeChild(removing);
	customSeqNumCheck.splice(customSeqNumCheck.indexOf(num), 1);
}

function IGVTDFSelection(id){
	var value = document.getElementById(id).value;
	var id_num = id.split("_")[2];
	console.log(id_num);
	if (value == 'yes') {
		document.getElementById('label_1_'+id_num).setAttribute("style", "display:show");
		document.getElementById('textarea_2_'+id_num).setAttribute("style", "display:show");
	}else{
		document.getElementById('label_1_'+id_num).setAttribute("style", "display:none");
		document.getElementById('textarea_2_'+id_num).setAttribute("style", "display:none");
		document.getElementById('textarea_2_'+id_num).value = 0;
	}
}

function MCallSelection(id){
	var check = document.getElementById('checkbox_4_'+id).checked;
	if (check) {
		document.getElementById('label_2_'+id).setAttribute("style", "display:show");
		document.getElementById('multi_select_1_'+id).setAttribute("style", "display:show");
		document.getElementById('label_3_'+id).setAttribute("style", "display:show");
		document.getElementById('multi_select_2_'+id).setAttribute("style", "display:show");
		document.getElementById('label_4_'+id).setAttribute("style", "display:show");
		document.getElementById('textarea_3_'+id).setAttribute("style", "display:show");
	}else{
		document.getElementById('label_2_'+id).setAttribute("style", "display:none");
		document.getElementById('multi_select_1_'+id).setAttribute("style", "display:none");
		document.getElementById('label_3_'+id).setAttribute("style", "display:none");
		document.getElementById('multi_select_2_'+id).setAttribute("style", "display:none");
		document.getElementById('label_4_'+id).setAttribute("style", "display:none");
		document.getElementById('textarea_3_'+id).setAttribute("style", "display:none");
	}
}

function MCompSelection(id){
	var check = document.getElementById('checkbox_5_'+id).checked;
	if (check) {
		document.getElementById('label_5_'+id).setAttribute("style", "display:show");
		document.getElementById('textarea_4_'+id).setAttribute("style", "display:show");
	}else{
		document.getElementById('label_5_'+id).setAttribute("style", "display:none");
		document.getElementById('textarea_4_'+id).setAttribute("style", "display:none");
	}
}

function changeRNAParamsBtn(){
	if(document.getElementById('change_params_inner') != undefined){
	document.getElementById('change_params_outer').removeChild(document.getElementById('change_params_inner'));
	}else{
	var outerDiv = document.getElementById('change_params_outer');

	var innerDiv = createElement('div', ['id', 'style'], ['change_params_inner', 'display:"none"']);
	var label = createElement('label', ['class','TEXTNODE'], ['box-title', 'Bowtie Parameters']);
	var textBox = createElement('input', ['id', 'class', 'type', 'value'], ['change_params_val', 'form-control', 'text', '-N 1']);

	innerDiv.appendChild(label);
	innerDiv.appendChild(textBox);
	outerDiv.appendChild(innerDiv);
	}
}

function selectTrimming(select_id, five_num, three_num) {
	var trim_select = document.getElementById(select_id);
	var trim_parent_parent = trim_select.parentNode.parentNode
	var single_option = trim_select.childNodes[0];
	var paired_option = trim_select.childNodes[1];

	if (trim_select.value == paired_option.value) {
	var five_len = createElement('div', ['class'], ['col-md-6']);
	var three_len = createElement('div', ['class'], ['col-md-6']);

	five_len.appendChild(createElement('label', ['class','TEXTNODE'], ['box-title', '5 length 2']));
	five_len.appendChild(createElement('input', ['id', 'class', 'type', 'value'], ['5 length 2_val', 'form-control', 'text', five_num]));
	trim_parent_parent.appendChild(five_len);

	three_len.appendChild(createElement('label', ['class','TEXTNODE'], ['box-title', '3 length 2']));
	three_len.appendChild(createElement('input', ['id', 'class', 'type', 'value'], ['3 length 2_val', 'form-control', 'text', three_num]));
	trim_parent_parent.appendChild(three_len);
	}else{
	trim_parent_parent.removeChild(trim_parent_parent.childNodes[5]);
	trim_parent_parent.removeChild(trim_parent_parent.childNodes[4]);
	}
}

function bisulphiteSelect(id, num){
	if (id == num+'_RRBS') {
		var dig_site = document.getElementById('text_1_'+num);
		dig_site.disabled = false;
		dig_site.value = 'C-CGG';
	}else{
		var dig_site = document.getElementById('text_1_'+num);
		dig_site.disabled = true;
		dig_site.value = '';
	}
}
