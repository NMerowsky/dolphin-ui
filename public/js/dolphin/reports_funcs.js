/*
 *Author: Nicholas Merowsky
 *Date: 09 Apr 2015
 *Ascription:
 */

var wkey = '';
var lib_checklist = [];
var nameAndDirArray = [];
var currentResultSelection = '--- Select a Result ---';

function parseTSV(report, jsonName, nameAndDirArray){
	var basePath = 'http://galaxyweb.umassmed.edu/csv-to-api/?source=/project/umw_biocore/pub/ngstrack_pub';
	var URL = nameAndDirArray[1][0] + 'counts/' + report + '.summary.tsv&fields=' + jsonName;
	var parsed = [];
	var parsePushed = [];

	$.ajax({ type: "GET",
			 url: basePath + URL,
			 async: false,
			 success : function(s)
			 {
							jsonGrab = s.map(JSON.stringify);
							for(var i = 0; i < jsonGrab.length - 1; i++){
								//limit is length minus one, last element is empty
								parsed = JSON.parse(jsonGrab[i]);
								parsePushed.push(parsed[jsonName]);
							}
						 }
	});
	return parsePushed;
}

function parseMoreTSV(report, jsonNameArray, nameAndDirArray){
	var basePath = 'http://galaxyweb.umassmed.edu/csv-to-api/?source=/project/umw_biocore/pub/ngstrack_pub';
	var URL = nameAndDirArray[1][0] + 'counts/' + report + '.summary.tsv&fields=' + jsonNameArray;
	var parsed = [];
	var parsePushed = [];

	$.ajax({ type: "GET",
			 url: basePath + URL,
			 async: false,
			 success : function(s)
			 {
							jsonGrab = s.map(JSON.stringify);
							for(var i = 0; i < jsonGrab.length - 1; i++){
								//limit is length minus one, last element is empty
								parsed = JSON.parse(jsonGrab[i]);
								for(var k = 0; k < jsonNameArray.length; k++){
									parsePushed.push(parsed[jsonNameArray[k]]);
								}
							}
						 }
	});
	return parsePushed;
}

//implement wkey after testing

function getDownloadText(dataType, downloadType) {
	var basePath = 'http://galaxyweb.umassmed.edu/csv-to-api/?source=/project/umw_biocore/pub/ngstrack_pub';
	var URL = checkFrontAndEndDir('mousetest') + '/counts/' + dataType + '.counts.tsv&fields=id,' + lib_checklist.toString() + '&format=' + downloadType;

	var stringData = '';

	$.ajax({ type: "GET",
			 url: basePath + URL,
			 async: false,
			 success : function(s)
			 {
				stringData = s;
						 }
	});
	return stringData;
}

function createSummary(nameAndDirArray) {
	var basePath = 'http://galaxyweb.umassmed.edu/pub/ngstrack_pub' + nameAndDirArray[1][0] + 'fastqc/UNITED';
	var linkRef = [ '/per_base_quality.html', '/per_base_sequence_content.html', '/per_sequence_quality.html'];
	var linkRefName = ['Per Base Quality Summary', 'Per Base Sequence Content Summary', 'Per Sequence Quality Summary'];

	var masterDiv = document.getElementById('summary_exp_body');

	for(var x = 0; x < linkRefName.length; x++){
	var link = createElement('a', ['href'], [basePath + linkRef[x]]);
	link.appendChild(document.createTextNode(linkRefName[x]));
	masterDiv.appendChild(link);
	masterDiv.appendChild(createElement('div', [],[]));
	}
}

function createDetails(nameAndDirArray) {
	var basePath = 'http://galaxyweb.umassmed.edu/pub/ngstrack_pub' + nameAndDirArray[1][x];

	var masterDiv = document.getElementById('details_exp_body');

	for(var x = 0; x < nameAndDirArray[0].length; x++){
	var splt1 = nameAndDirArray[0][x].split(',');
	for(var y = 0; y < splt1.length; y++){
		var link = createElement('a', ['href'], [basePath + splt1[y]]);
		link.appendChild(document.createTextNode(splt1[y]));
		masterDiv.appendChild(link);
		masterDiv.appendChild(createElement('div', [],[]));
	}
	}
}

//to me used/moved at a later date
function createPlot(){
	var url="http://galaxyweb.umassmed.edu/csv-to-api/?source=http://galaxyweb.umassmed.edu/pub/ngstrack_pub/mousetest/rsem/genes_expression_expected_count.tsv";
	var masterDiv = document.getElementById('plots_exp_body');
	var headDiv = document.getElementById('plots_exp');

	var overlay = createElement('div', ['id', 'class'],['overlay', 'overlay']);
	overlay.appendChild(createElement('i', ['class'], ['fa fa-refresh fa-spin']));
	headDiv.appendChild(overlay);

	d3.json(url, draw);
}

/* checkFrontAndEndDir function
 *
 * checks to make sure that the outdir specified has
 * both '/'s on either end in order to be used by whichever
 * function requires the addition of the outdir
 */

function checkFrontAndEndDir(wkey){
	if (wkey[0] != '/') {
		wkey = '/' + wkey;
	}
	if (wkey[wkey.length - 1] != '/') {
		wkey = wkey + '/';
	}
	return wkey;
}

function cleanReports(reads, totalReads){
	var perc = (reads/totalReads).toFixed(3);
	var stringPerc = "" + reads + " (" + perc + "%)";
	return stringPerc;
}

function storeLib(name){
	if (lib_checklist.indexOf(name) > -1) {
		lib_checklist.splice(lib_checklist.indexOf(name), 1);
	}else{
		lib_checklist.push(name);
	}
}

function createDropdown(nameList){
	var masterDiv = document.getElementById('initial_mapping_exp_body');
	var childDiv = createElement('div', ['id', 'class'], ['select_div', 'input-group margin col-md-4']);
	var selectDiv = createElement('div', ['id', 'class'], ['inner_select_div', 'input-group-btn margin']);

	selectDiv.appendChild( createElement('select',
					['id', 'class', 'onchange', 'OPTION_DIS_SEL', 'OPTION', 'OPTION', 'OPTION', 'OPTION', 'OPTION'],
					['select_report', 'form-control', 'showSelectTable()', '--- Select a Result ---',
					nameList[0], nameList[1], nameList[2], nameList[3], nameList[4] ]));
	childDiv.appendChild(selectDiv);
	masterDiv.appendChild(childDiv);
}

function showSelectTable(){
	if (lib_checklist.length < 1) {
		alert("Libraries must be selected to view these reports")
		document.getElementById('select_report').value = currentResultSelection;
	}else{
		currentResultSelection = document.getElementById('select_report').value;
		var masterDiv = document.getElementById('initial_mapping_exp_body');
		
		if (document.getElementById('jsontable_selected_results') == null) {
			var buttonDiv = createElement('div', ['id', 'class'], ['clear_button_div', 'input-group margin']);
			var buttonDivInner = createElement('div', ['id', 'class'], ['clear_button_inner_div', 'input-group margin pull-left']);
			var clearButton = createElement('input', ['id', 'type', 'value', 'class', 'onclick'], ['clear_button', 'button', 'Clear Selection', 'btn btn-primary', 'clearSelection()']);
			buttonDivInner.appendChild(clearButton);
			buttonDiv.appendChild(buttonDivInner);
			buttonDiv.appendChild(createDownloadReportButtons());
			masterDiv.appendChild(buttonDiv);
			
			var table = generateSelectionTable();
			masterDiv.appendChild(table);
		}else{
			var table = document.getElementById('jsontable_selected_results');
			var newTable = generateSelectionTable();
			$('#jsontable_selected_results_wrapper').replaceWith(newTable);
		}
	
		var newTableData = $('#jsontable_selected_results').dataTable();
		newTableData.fnClearTable();
		var objList = getCountsTableData(currentResultSelection).map(JSON.stringify);
		for(var x = 0; x < objList.length; x++){
			var parsed = JSON.parse(objList[x]);
			var jsonArray = [];
			for( var i in parsed){
				if (parsed[i] != null) {
					jsonArray.push(parsed[i]);
				}
			}
			if (jsonArray.length > 0) {
				newTableData.fnAddData(jsonArray);
			}
		}
		//newTableData.fnSort( [ [0,'asc'] ] );
		newTableData.fnAdjustColumnSizing(true);
	}
}

function clearSelection(){
	document.getElementById('jsontable_selected_results_wrapper').remove();
	document.getElementById('clear_button_div').remove();
	document.getElementById('select_report').value = '--- Select a Result ---';
}

function generateSelectionTable(){
	var newTable = createElement('table', ['id', 'class'], ['jsontable_selected_results', 'table table-hover compact']);
	var thead = createElement('thead', [], []);
	var header = createElement('tr', ['id'], ['selected_header']);
	var thID = createElement('th', [], []);
		thID.innerHTML = 'id';
		header.appendChild(thID);
	for(var x = 0; x < lib_checklist.length; x++){
		var th = createElement('th', [], []);
		th.innerHTML = lib_checklist[x];
		header.appendChild(th);
	}
	thead.appendChild(header);
	newTable.appendChild(thead);
	return newTable;
}

function getCountsTableData(currentResultSelection){
	var basePath = 'http://galaxyweb.umassmed.edu/csv-to-api/?source=/project/umw_biocore/pub/ngstrack_pub';
	var URL = nameAndDirArray[1][0] + 'counts/' + currentResultSelection + '.counts.tsv&fields=id,' + lib_checklist;

	var objList = [];

	$.ajax({ type: "GET",
			url: basePath + URL,
			async: false,
			success : function(s)
			{
				objList = s;
			}
	});
	return objList;
}

function createDownloadReportButtons(){
	var downloadDiv = createElement('div', ['id', 'class'], ['downloads_div', 'btn-group margin pull-right']);
	var buttonType = ['JSON','JSON2', 'XML', 'HTML'];
	for (var x = 0; x < buttonType.length; x++){
		var button = createElement('input', ['id', 'class', 'type', 'value', 'onclick'], [buttonType[x], 'btn btn-primary', 'button', buttonType[x], 'downloadReports("'+buttonType[x]+'")']);
		downloadDiv.appendChild(button);
	}

	return downloadDiv;
	/*
	 *File download:
		var stringData = getDownloadText(dataType, downloadType);
		var data = "text/json;charset=utf-8," + encodeURIComponent(stringData);
		// what to return in order to show download window?
		button.setAttribute("href", "data:"+data);
		button.setAttribute("download", dataType + "." + downloadType);
	*/
}

function downloadReports(type){
	var basePath = 'http://galaxyweb.umassmed.edu/csv-to-api/?source=/project/umw_biocore/pub/ngstrack_pub';
	var countsType = document.getElementById('select_report').value;
	var URL = checkFrontAndEndDir(wkey) + 'counts/' + countsType + '.counts.tsv&fields=id,' + lib_checklist + '&format=' + type;
	
	if (type == 'JSON') {
		//Download actual file in the future?
		window.open(basePath + URL);
	}else{
		window.open(basePath + URL);
	}
}

$(function() {
	"use strict";
	if (phpGrab.theSegment == 'report') {
	var reports_table = $('#jsontable_initial_mapping').dataTable();
	var basePath = 'http://galaxyweb.umassmed.edu/csv-to-api/?source=/project/umw_biocore/pub/ngstrack_pub';

	var hrefSplit = window.location.href.split("/");
	
	wkey = 'mousetest'; //hrefSplit[hrefSplit.length - 2];
	
	var runId = hrefSplit[hrefSplit.length - 2];
	var samples = hrefSplit[hrefSplit.length - 1].substring(0, hrefSplit[hrefSplit.length - 1].length - 1).split(",");
	nameAndDirArray = getSummaryInfo(runId, samples);

	nameAndDirArray = [['','',''],['mousetest','','']];

	for(var x = 0; x < nameAndDirArray[1].length; x++){
		nameAndDirArray[1][x] = checkFrontAndEndDir(nameAndDirArray[1][x]);
	}

	createSummary(nameAndDirArray);
	createDetails(nameAndDirArray);

	var jsonGrab = parseMoreTSV('rRNA', ['File','Total Reads','Reads 1'], nameAndDirArray);
	var miRNA = parseTSV('miRNA', 'Reads 1', nameAndDirArray);
	var tRNA= parseTSV('tRNA', 'Reads 1', nameAndDirArray);
	var snRNA = parseTSV('snRNA', 'Reads 1', nameAndDirArray);
	var rmsk = parseMoreTSV('rmsk', ['Reads 1','Unmapped Reads'], nameAndDirArray);
	var libnames = ['rRNA', 'miRNA', 'tRNA', 'snRNA', 'rmsk'];

	//Initial Mapping Results
	reports_table.fnClearTable();
	for (var x = 0; x < miRNA.length; x++) {
		reports_table.fnAddData([
		jsonGrab[x * 3],
		jsonGrab[(x * 3) + 1],
		cleanReports(jsonGrab[(x * 3) + 2].split(" ")[0], jsonGrab[(x * 3) + 1]),
		cleanReports(miRNA[x].split(" ")[0], jsonGrab[(x * 3) + 1]),
		cleanReports(tRNA[x].split(" ")[0], jsonGrab[(x * 3) + 1]),
		cleanReports(snRNA[x].split(" ")[0], jsonGrab[(x * 3) + 1]),
		cleanReports(rmsk[x * 2].split(" ")[0], jsonGrab[(x * 3) + 1]),
		cleanReports(rmsk[(x * 2) + 1].split(" ")[0], jsonGrab[(x * 3) + 1]),
		"<input type=\"checkbox\" class=\"ngs_checkbox\" name=\"" + jsonGrab[x * 3] + "\" id=\"lib_checkbox_"+x+"\" onClick=\"storeLib(this.name)\">",
		]);
	}

	createDropdown(libnames);

	reports_table.fnSort( [ [0,'asc'] ] );
	reports_table.fnAdjustColumnSizing(true);
	}
});
