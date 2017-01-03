/*
 *Author: Nicholas Merowsky
 *Date: 09 Apr 2015
 *Ascription:
 */

var wkey = '';
var lib_checklist = [];
var libraries = [];
var dash_library = [];
var table_array = [];
var currentResultSelection = '--- Select a Result ---';
var tableDirectionNum = 0;
var table_data = {};
var headers = [];
var type_dictionary = ['rRNA', 'miRNA', 'piRNA', 'tRNA', 'snRNA', 'rmsk', 'ercc'];
var summary_RNA = [];
var initial_mapping_table = [];

function parseSummary(url_path){
	var parsedArray = [];
	console.log(API_PATH + "/public/api/?source=" + API_PATH + "/public/pub/" + wkey + "/" + url_path);
	$.ajax({ type: "GET",
			url: API_PATH + "/public/api/?source=" + API_PATH + "/public/pub/" + wkey + "/" + url_path,
			async: false,
			success : function(s)
			{
				for( var j = 0; j < s.length; j++){
					var parsed = s[j];
					parsedArray.push(parsed);
				}
			}
	});
	return parsedArray;
}

function parseTSV(jsonName, url_path){
	var parsedArray = [];
	$.ajax({ type: "GET",
			url: API_PATH + "/public/api/?source=" + API_PATH + "/public/pub/" + wkey + "/" + url_path,
			async: false,
			success : function(s)
			{
				for( var j = 0; j < s.length; j++){
					var parsed = [];
					parsed.push(s[j][jsonName]);
					parsedArray.push(parsed);
				}
			}
	});
	return parsedArray;
}

function parseMoreTSV(jsonNameArray, url_path){
	console.log(API_PATH + "/public/api/?source=" + API_PATH + "/public/pub/" + wkey + "/" + url_path);
	var parsedArray = [];
	$.ajax({ type: "GET",
			url: API_PATH + "/public/api/?source=" + API_PATH + "/public/pub/" + wkey + "/" + url_path,
			async: false,
			success : function(s)
			{
				for( var j = 0; j < s.length; j++){
					var parsed = [];
					for(var k = 0; k < jsonNameArray.length; k++){
						parsed.push(s[j][jsonNameArray[k]]);
					}
					parsedArray.push(parsed);
				}
			}
	});
	return parsedArray;
}

function parseFlagstat(url_path) {
	var mapped = '';
	$.ajax({ type: "GET",
			url: BASE_PATH + "/public/pub/" + wkey + "/" + url_path,
			async: false,
			success : function(s)
			{
				console.log(s.split("\n"));
				var flag_array = s.split("\n");
				if(s.split("\n").length > 3){
					console.log(flag_array[9].split(" ")[0]);
					console.log(flag_array[10].split(" ")[0]);
					mapped = (parseInt(flag_array[9].split(" ")[0]) / 2) + parseInt(flag_array[10].split(" ")[0]);
					if (mapped == 0) {
						mapped = (parseInt(flag_array[4].split(" ")[0]));
					}
				}else{
					mapped = s.trim();
				}
			}
	});
	return mapped;
}

function parseDedup(url_path) {
	var dedup = {};
	$.ajax({ type: "GET",
			url: BASE_PATH + "/public/pub/" + wkey + "/" + url_path,
			async: false,
			success : function(s)
			{
				console.log(s);
				var array = s.split("\n");
				for (var x = 0; x < array.length; x++) {
					var name = array[x].split("/")[array[x].split("/").length - 1].split("PCR_duplicates")[0];
                    name = name.slice(0,-1);
					dedup[name] = array[x].split(" ")[1]
				}
			}
	});
	return dedup;
}

function createSummary(fastqc_summary) {
	if (fastqc_summary) {
		var linkRef = [ '/per_base_quality.html', '/per_base_sequence_content.html', '/per_sequence_quality.html'];
		var linkRefName = ['Per Base Quality Summary', 'Per Base Sequence Content Summary', 'Per Sequence Quality Summary'];
	
		var masterDiv = document.getElementById('summary_exp_body');
	
		for(var x = 0; x < linkRefName.length; x++){
			var link = createElement('a', ['href'], [BASE_PATH + '/public/pub/' + wkey + '/fastqc/UNITED' + linkRef[x]]);
			link.appendChild(document.createTextNode(linkRefName[x]));
			masterDiv.appendChild(link);
			masterDiv.appendChild(createElement('div', [],[]));
		}
	}
}

function createDetails(libraries) {
	var masterDiv = document.getElementById('details_exp_body');
	var run_id = '0';
	$.ajax({ type: "GET",
		url: BASE_PATH +"/ajax/sessionrequests.php",
		data: { p: 'getReportsRunID' },
		async: false,
		success : function(s)
		{
			var returnedSamples = s.split(',');
			console.log(returnedSamples);
			for(var x = 0; x < s.length; x++){
				if (x == 0) {
					run_id = returnedSamples[x];
				}
			}
		}
	});
	var wkey = getReportWKey(run_id);
	var pairCheck = findIfMatePaired(run_id);
	
	for(var x = 0; x < libraries.length; x++){
		if (pairCheck) {
			var link1 = createElement('a', ['href'], [BASE_PATH + '/public/pub/' + wkey + '/fastqc/' + libraries[x] + '.1/' + libraries[x] + '.1_fastqc/fastqc_report.html']);
			link1.appendChild(document.createTextNode(libraries[x] + ".1"));
			var link2 = createElement('a', ['href'], [BASE_PATH + '/public/pub/' + wkey + '/fastqc/' + libraries[x] + '.2/' + libraries[x] + '.2_fastqc/fastqc_report.html']);
			link2.appendChild(document.createTextNode(libraries[x] + ".2"));
			masterDiv.appendChild(link1);
			masterDiv.appendChild(createElement('div', [],[]));
			masterDiv.appendChild(link2);
			masterDiv.appendChild(createElement('div', [],[]));
		}else{
			var link = createElement('a', ['href'], [BASE_PATH + '/public/pub/' + wkey + '/fastqc/' + libraries[x] + '/' + libraries[x] + '_fastqc/fastqc_report.html']);
			link.appendChild(document.createTextNode(libraries[x]));
			masterDiv.appendChild(link);
			masterDiv.appendChild(createElement('div', [],[]));
		}
		
		
	}
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

function createDropdown(mapping_list, type){
	var masterDiv = document.getElementById(type+'_exp_body');
	var childDiv = createElement('div', ['id', 'class'], ['select_'+ type + '_div', 'input-group margin col-md-12']);
	var selectDiv = createElement('div', ['id', 'class'], ['inner_select_' + type + '_div', 'margin col-md-4']);

	selectDiv.appendChild( createElement('select',
					['id', 'class', 'onchange', 'OPTION_DIS_SEL'],
					['select_' + type + '_report', 'form-control', 'showTable("'+type+'")', '--- Select a Result ---']));
	childDiv.appendChild(selectDiv);
	masterDiv.appendChild(childDiv);
	for (var x = 0; x < mapping_list.length; x++){
		var opt = createElement('option', ['id','value'], [mapping_list[x], mapping_list[x]]);
		opt.innerHTML = mapping_list[x];
		document.getElementById('select_' + type + '_report').appendChild(opt);
	}
}

function obtainObjectKeys(obj){
	var keys = [];
	for (var key in obj) {
		if (obj.hasOwnProperty(key)) {
			keys.push(key)
		}
	}
	return keys;
}

function showTable(type){
	var ordered_lib_checklist = [];
	for (var x = 0; x < libraries.length; x++){
		if (lib_checklist.indexOf(libraries[x]) != -1) {
			ordered_lib_checklist.push(libraries[x]);
		}
	}
	lib_checklist = ordered_lib_checklist;
	temp_libs = [null];
	if (lib_checklist.length <= 0) {
		temp_libs = lib_checklist;
		lib_checklist = libraries;
	}
	
	currentResultSelection = document.getElementById('select_' + type + '_report').value;
	var temp_currentResultSelection;
	var objList;
	
	if (type == 'initial_mapping') {
		temp_currentResultSelection = 'counts/' + currentResultSelection + '.counts.tsv&fields=id,' + lib_checklist.toString().replace(/-/g,"_");
		console.log(API_PATH + "/public/api/?source=" + API_PATH + '/public/pub/' + wkey + '/' + temp_currentResultSelection);
	}else{
		temp_currentResultSelection = currentResultSelection;
	}
	
	$.ajax({ type: "GET",
			url: API_PATH + "/public/api/?source=" + API_PATH + '/public/pub/' + wkey + '/' + temp_currentResultSelection,
			async: false,
			success : function(s)
			{
				console.log(s);
				objList = s;
			}
	});
	for(var d = 0; d < objList.length; d++){
		var keys = obtainObjectKeys(objList[d]);
		var newObj = {};
		for(var c = 0; c < keys.length; c++){
			if(!isNaN(parseFloat(keys[c][0])) && isFinite(keys[c][0])){
				keys[c] = "_" + keys[c];
			}
			newObj[keys[c].replace(/\./g, "_")] = objList[d][keys[c]];
		}
		objList[d] = newObj
	}
	var keys = obtainObjectKeys(objList[0]);
	console.log(keys)
	
	if(currentResultSelection.split(".")[currentResultSelection.split(".").length - 1] == "tsv" || type_dictionary.indexOf(currentResultSelection) > -1){
		highchartDivCreate(type, keys)
		
		createStreamScript(keys, type)
		var data = objList, html = $.trim($("#template_"+type).html()), template = Mustache.compile(html);
		console.log(keys);
		var view = function(record, index){
			var mergeRecords = '<tr>';
			for(var x = 0; x < keys.length; x++){
				mergeRecords += '<td>';
				mergeRecords += record[keys[x]];
				mergeRecords += '</td>';
			}
			mergeRecords += '</tr>';
			return mergeRecords;
		};
		
		var callbacks = {
			after_add: function(){
				//Only for example: Stop ajax streaming beacause from localfile data size never going to empty.
				if (this.data.length == objList.length){
					this.stopStreaming();
				}
		
			}
		}
		st = StreamTable('#jsontable_' + type + '_results',
		  { view: view, 
			per_page: 10, 
			data_url: API_PATH + "/public/api/?source=" + API_PATH + '/public/pub/' + wkey + '/' + temp_currentResultSelection,
			stream_after: 0.2,
			fetch_data_limit: 100,
			callbacks: callbacks,
			pagination:{
				span: 5,                              
				next_text: 'Next &rarr;',              
				prev_text: '&larr; Previous',
				ul_class: type,
			},
		  },
		 data, type);
		
		var search = document.getElementById('st_search');
		search.id = 'st_search_' + type;
		search.setAttribute('class',"st_search margin pull-right");
		
		var num_search = document.getElementById('st_num_search');
		num_search.id = 'st_num_search_' + type;
		
		var newlabel = createElement('label', ['class'], ['margin']);
		newlabel.setAttribute("for",'st_num_search_'+type);
		newlabel.innerHTML = " entries per page";
		document.getElementById(type+'_table_div').insertBefore(newlabel, num_search);
		
		num_search.setAttribute('class',"st_per_page margin pull-left");
		
		document.getElementById('st_pagination').id = 'st_pagination_' + type;
		document.getElementById('st_pagination_'+type).setAttribute('class',"st_pagination_"+type+" margin");
		document.getElementById('st_pagination_'+type).setAttribute('style',"text-align:right");
		
		if (temp_libs.length <= 0) {
			lib_checklist = [];
		}
		
		if (type == 'rseqc' && /counts.tsv/.test(currentResultSelection)) {
			rseqcPlotGen(type, objList, type+'_table_div')
		}
	}else{
		var masterDiv = document.getElementById('select_'+type+'_div');
		if (document.getElementById('clear_' + type + '_button_div') == null) {
			var buttonDiv = createElement('div', ['id', 'class'], ['clear_' + type + '_button_div', 'margin col-md-4']);
			buttonDiv.appendChild(createDownloadReportButtons(currentResultSelection, type));
			masterDiv.appendChild(buttonDiv);
			if (document.getElementById('jsontable_' + type + '_results_wrapper') != null) {
				document.getElementById('jsontable_' + type + '_results_wrapper').remove();
			}
		}else{
			var buttonDiv = createElement('div', ['id', 'class'], ['clear_' + type + '_button_div', 'margin col-md-4']);
			buttonDiv.appendChild(createDownloadReportButtons(currentResultSelection, type));
			$('#clear_' + type + '_button_div').replaceWith(buttonDiv);
			if (document.getElementById('jsontable_' + type + '_results_wrapper') != null) {
				document.getElementById('jsontable_' + type + '_results_wrapper').remove();
			}
		}
	}
	
}

function highchartDivCreate(type, keys) {
	var masterDiv = document.getElementById(type+'_exp_body');
	var tableDiv = createElement('div', ['id', 'class', 'style'], [type+'_table_div', 'panel panel-default margin', 'overflow-x:scroll']);
	var selectDiv = document.getElementById('select_'+type+'_div');
	if (document.getElementById('jsontable_' + type + '_results') == null) {
		var previous_button = false;
		if (document.getElementById('clear_' + type + '_button_div') != null) {
			previous_button = true;
		}
		var buttonDiv = createElement('div', ['id', 'class'], ['clear_' + type + '_button_div', 'margin col-md-4']);
		buttonDiv.appendChild(createDownloadReportButtons(currentResultSelection, type));
		if (previous_button) {
			$('#clear_' + type + '_button_div').replaceWith(buttonDiv);
		}else{
			selectDiv.appendChild(buttonDiv);
		}
		var table = generateSelectionTable(keys, type);
		tableDiv.appendChild(table)
		masterDiv.appendChild(tableDiv);
	}else{
		document.getElementById(type+'_table_div').remove();
		document.getElementById('template_'+type).remove();
		
		tableDiv = createElement('div', ['id', 'class', 'style'], [type+'_table_div', 'panel panel-default margin', 'overflow-x:scroll']);
		var table = generateSelectionTable(keys, type);
		tableDiv.appendChild(table)
		masterDiv.appendChild(tableDiv);
	}
}

function rseqcPlotGen(type, objList, headDiv){
	var name_gather_bool = true;
	var rseqc_categories = [];
	var rseqc_series = [];
	for (var cat in objList) {
		var series_object = {};
		var data_array = [];
		for (ser in objList[cat]) {
			if (ser == 'region') {
				series_object['name'] = objList[cat][ser];
			}else{
				if (name_gather_bool) {
					rseqc_categories.push(ser);
				}
				data_array.push(parseInt(objList[cat][ser]));
			}
		}
		name_gather_bool = false;
		series_object['data'] = data_array;
		rseqc_series.push(series_object);
	}
	
	document.getElementById(headDiv).appendChild(createElement('button', ['id', 'class', 'onclick'], ['switch_plot', 'btn btn-primary margin', 'switchStacking("'+headDiv+'", "'+type+'_plot")']))
	document.getElementById('switch_plot').innerHTML = 'Switch Plot Type';
	createHighchart(rseqc_categories, rseqc_series, 'RSeQC Count Results', 'Comparitive Sample Percentages', headDiv, type+'_plot', 'percent');
	showHighchart(headDiv);
}

function createStreamScript(keys, type){
	var masterScript = createElement('script', ['id', 'type'], ['template_'+type, 'text/html']);
	var tr = createElement('tr', [], []);
	
	for(var x = 0; x < keys.length; x++){
		var td = createElement('td', [], []);
		td.innerHTML = "{{record."+keys[x]+"}}";
		tr.appendChild(td);
	}
	masterScript.appendChild(tr);
	document.getElementsByTagName('body')[0].appendChild(masterScript);
}

function clearSelection(type){
	if (document.getElementById('jsontable_' + type + '_results_wrapper') != null) {
		document.getElementById('jsontable_' + type + '_results_wrapper').remove();
	}
	document.getElementById('clear_' + type + '_button_div').remove();
	document.getElementById(type+'_table_div').remove();
	document.getElementById('select_' + type + '_report').value = '--- Select a Result ---';
}

function generateSelectionTable(keys, type){
	var newTable = createElement('table', ['id', 'class'], ['jsontable_' + type + '_results', 'table table-hover compact']);
	var thead = createElement('thead', [], []);
	var tbody = createElement('tbody', [], []);
	var header = createElement('tr', ['id'], [type + '_header']);
	var temp_lib_checklist;
	if (lib_checklist.length == 0) {
		temp_lib_checklist = libraries;
	}else{
		temp_lib_checklist = lib_checklist;
	}
	
	if (type == 'initial_mapping') {
		for(var x = 0; x < keys.length; x++){
			if (temp_lib_checklist.indexOf(keys[x]) > -1 || dash_library.indexOf(keys[x]) > -1) {
				var th = createElement('th', ['data-sort', 'onclick'], [keys[x]+'::number', 'shiftColumns(this)']);
				th.innerHTML = keys[x];
				th.appendChild(createElement('i', ['id', 'class'], [keys[x], 'pull-right fa fa-unsorted']));
				header.appendChild(th);
			}else if (keys[x] == "id" || keys[x] == "name" || keys[x] == "len" || keys[x] == "gene" || keys[x] == "transcript") {
				var th = createElement('th', ['data-sort', 'onclick'], [keys[x]+'::string', 'shiftColumns(this)']);
				th.innerHTML = keys[x];
				th.appendChild(createElement('i', ['id', 'class'], [keys[x], 'pull-right fa fa-unsorted']));
				header.appendChild(th);
			}
		}
	}else{
		for(var x = 0; x < keys.length; x++){
			if (libraries.indexOf(keys[x]) > -1 || keys[x] == 'padj' || keys[x] == 'log2FoldChange' || keys[x] == 'foldChange' || dash_library.indexOf(keys[x]) > -1) {
				var th = createElement('th', ['data-sort', 'onclick'], [keys[x]+'::number', 'shiftColumns(this)']);
			}else{
				var th = createElement('th', ['data-sort', 'onclick'], [keys[x]+'::string', 'shiftColumns(this)']);
			}
			th.innerHTML = keys[x];
			th.appendChild(createElement('i', ['id', 'class'], [keys[x], 'pull-right fa fa-unsorted']));
			header.appendChild(th);
		}
	}
	
	thead.appendChild(header);
	newTable.appendChild(thead);
	newTable.appendChild(tbody);
	return newTable;
}

function shiftColumns(id){
	if (id.childNodes[1].getAttribute('class') == 'pull-right fa') {
		id.childNodes[1].setAttribute('class', 'pull-right fa fa-sort-asc');
	}else if (id.childNodes[1].getAttribute('class') == 'pull-right fa fa-sort-asc') {
		id.childNodes[1].setAttribute('class','pull-right fa fa-sort-desc');
	}else{
		id.childNodes[1].setAttribute('class','pull-right fa fa-sort-asc');
	}
	
}

function createDownloadReportButtons(currentSelection, type){
	var downloadDiv = createElement('div', ['id', 'class'], ['downloads_' + type + '_div', 'btn-group']);
	var ul = createElement('ul', ['class', 'role'], ['dropdown-menu', 'menu']);
	var button = createElement('button', ['type', 'class', 'data-toggle', 'aria-expanded'], ['button', 'btn btn-primary dropdown-toggle', 'dropdown', 'true'])
	button.innerHTML = 'Select Data Options ';
	var span = createElement('span', ['class'], ['fa fa-caret-down']);
	button.appendChild(span);
	
	if(currentResultSelection.split(".")[currentResultSelection.split(".").length - 1] == "tsv" || currentResultSelection.substring(currentResultSelection.length - 3, currentResultSelection.length) == "RNA" || currentResultSelection == 'ercc'){
		var buttonType = ['JSON','JSON2', 'XML', 'HTML'];
		for (var x = 0; x < buttonType.length; x++){
			var li = createElement('li', [], []);
			var a = createElement('a', ['onclick', 'style'], ['downloadReports("'+buttonType[x]+'", "'+type+'")', 'cursor:pointer']);
			a.innerHTML = buttonType[x] + ' link';
			li.appendChild(a);
			ul.appendChild(li);
		}
		ul.appendChild(createElement('li', ['class'], ['divider']));
		if(currentResultSelection.split("/")[0] == 'rsem' || currentResultSelection == 'mRNA' || currentResultSelection == 'tRNA'){
			var li = createElement('li', [], []);
			var a = createElement('a', ['onclick', 'style'], ['downloadReports("debrowser", "'+type+'")', 'cursor:pointer']);
			a.innerHTML = 'Send to DEBrowser';
			li.appendChild(a);
			ul.appendChild(li);
			ul.appendChild(createElement('li', ['class'], ['divider']));
		}
	}
	var TSV = createElement('li', [], []);
	var TSV_a = createElement('a', ['value', 'onclick', 'style'], ['Download File', 'downloadTSV("'+type+'")', 'cursor:pointer']);
	TSV_a.innerHTML = 'Download File';
	TSV.appendChild(TSV_a);
	ul.appendChild(TSV);
	ul.appendChild(createElement('li', ['class'], ['divider']));
	var clear = createElement('li', [], []);
	var clear_a = createElement('a', ['value', 'onclick', 'style'], ['Clear Selection', 'clearSelection("'+type+'")', 'cursor:pointer']);
	clear_a.innerHTML = 'Clear Selection';
	clear.appendChild(clear_a);
	ul.appendChild(clear);
	
	downloadDiv.appendChild(button);
	downloadDiv.appendChild(ul);

	return downloadDiv;
}

function downloadReports(buttonType, type){
	var temp_currentResultSelection;
	if (type == 'initial_mapping') {
		temp_currentResultSelection = 'counts/' + document.getElementById('select_'+type+'_report').value + '.counts.tsv';
	}else{
		temp_currentResultSelection = document.getElementById('select_'+type+'_report').value;
	}
	var URL = API_PATH + '/public/api/?source=' + API_PATH + '/public/pub/' + wkey + '/' + temp_currentResultSelection + '&format=' + buttonType;
	if (buttonType == 'debrowser') {
        URL = API_PATH + '/public/api/?source=' + API_PATH + '/public/pub/' + wkey + '/' + temp_currentResultSelection + '&format=JSON';
		$.ajax({ type: "GET",
                        url: BASE_PATH+"/public/ajax/sessionrequests.php",
                        data: { p: "sendToDebrowser", table: URL },
                        async: false,
                        success : function(s)
                        {
                        }
        });
        window.location.href = BASE_PATH + '/debrowser';
    }else{
		window.open(URL);
	}
}

function downloadTSV(type){
	var temp_currentResultSelection;
	if (type == 'initial_mapping') {
		temp_currentResultSelection = 'counts/' + currentResultSelection + '.counts.tsv';
	}else{
		temp_currentResultSelection = currentResultSelection;
	}
	var URL = BASE_PATH + '/public/pub/' + wkey + '/' + temp_currentResultSelection
	window.open(URL, '_blank');
}

function getReportWKey(run_id){
	var wkey = "";
	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/ngsquerydb.php",
			data: { p: 'getWKey', run_id: run_id },
			async: false,
			success : function(s)
			{
				wkey = s[0].wkey;
			}
	});
	return wkey;
}

function sendToPlots(){
	sendWKey(wkey);
	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/sessionrequests.php",
			data: { p: "setPlotToggle", type: 'normal', file: '' },
			async: false,
			success : function(s)
			{
			}
	});
	window.location.href = BASE_PATH+ '/plot';
}

function numberWithCommas(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function addPercentageArray(array){
	for (var x = 2; x < array.length; x++){
		if (array[x].toString().split(" ").length < 2) {
			array[x] = numberWithCommas(array[x] + " (" + ((array[x]/array[1])*100).toFixed(2) + " %)");
		}
	}
	array[1] = numberWithCommas(array[1]);
	return array
}

function populateSummaryTable(summary_files) {
	var summary_bool = false;
	var summary_index = 0;
	for(var x = 0; x < summary_files.length; x++){
		if (summary_files[x]['file'] == 'summary/summary.tsv') {
			summary_index = x;
			summary_bool = true;
		}
	}
	if (summary_bool) {
		var table_array = parseSummary(summary_files[summary_index]['file']);
		console.log(table_array);
		var reports_table = $('#jsontable_initial_mapping').dataTable();
		reports_table.fnClearTable();
		document.getElementById('jsontable_initial_mapping').setAttribute('style','overflow-x:scroll');
		var parsed = [];
		var headers = [];
		for( var j = 0; j < table_array.length; j++){
			if (j == 0){
				headers = Object.keys(table_array[j]);
				for(var x = 0; x < headers.length; x++){
					document.getElementById('tablerow').appendChild(createElement('th', ['id'], [headers[x]]));
					document.getElementById(headers[x]).innerHTML = headers[x];
				}
			}
			for( var i = 0; i < headers.length; i++){
				if (table_array[j][headers[i]] != undefined) {
					if (table_data[table_array[j]['Sample']] == undefined) {
						table_data[table_array[j]['Sample']] = {};
						parsed.push(table_array[j][headers[i]]);
					}else{
						table_data[table_array[j]['Sample']][headers[i]] = table_array[j][headers[i]];
						parsed.push(table_array[j][headers[i]]);
					}
				}
			}
			console.log(parsed)
			initial_mapping_table.push(parsed);
			parsed = [];
		}
		console.log(table_data)
		console.log(initial_mapping_table);
		var reports_table = $('#jsontable_initial_mapping').dataTable();
		reports_table.fnClearTable();
		document.getElementById('jsontable_initial_mapping').setAttribute('style','overflow-x:scroll');
		for(var y = 0; y < initial_mapping_table.length; y++){
			var row_array = initial_mapping_table[y];
			reports_table.fnAddData(addPercentageArray(row_array));
		}
	}else{
		document.getElementById('empty_div').innerHTML = '<h3 class="text-center">Your results have not been generated yet.  If your run has errored out, please contact your Dolphin Admin.' +
				'  If your run is currently running or queued, please be patient as the data is being generated.</h3>';
		document.getElementById('send_to_plots').disabled = true;
		document.getElementById('initial_mapping_exp').remove();
	}
}

function summaryPlotSetup(table_data){
	for (var sample_obj in table_data) {
		if (table_data[sample_obj]['rsem'] != undefined || table_data[sample_obj]['rsem_unique'] != undefined) {
			rsem_toggle = true;
			rsem_categories.push(sample_obj);
			for (var data in table_data[sample_obj]) {
				if (/rsem/.test(data)) {
					if (rsem_series[data] == undefined) {
						var name = data;
						if (data == 'rsem') {
							name = 'reads mapped'
						}else if (data == 'rsem_dedup') {
							name = 'dedup reads'
						}
						var num = table_data[sample_obj][data].toString().split(" ")[0].replace(/,/g, "");
						rsem_series[data] = {name: name, data: [parseInt(num)]}
					}else{
						var num = table_data[sample_obj][data].toString().split(" ")[0].replace(/,/g, "");
						rsem_series[data]['data'].push(parseInt(num))
					}
				}
			}
		}
		
		highchartSetup(sample_obj, 'tophat', tophat_categories, tophat_series)
		highchartSetup(sample_obj, 'chip', chip_categories, chip_series)
		highchartSetup(sample_obj, 'atac', atac_categories, atac_series)
		highchartSetup(sample_obj, 'star', star_categories, star_series)
		highchartSetup(sample_obj, 'hisat2', hisat2_categories, hisat2_series)
		
		if (table_data[sample_obj]['chip'] == undefined && table_data[sample_obj]['tophat'] == undefined && table_data[sample_obj]['rsem'] == undefined &&
			table_data[sample_obj]['atac'] == undefined && table_data[sample_obj]['star'] == undefined && table_data[sample_obj]['hisat2'] == undefined) {
			base_categories.push(sample_obj);
			for (var data in table_data[sample_obj]) {
				if (!/rsem/.test(data) && !/tophat/.test(data) && !/chip/.test(data) && !/total_reads/.test(data) &&
					!/atac/.test(data) && !/star/.test(data) && !/hisat2/.test(data)) {
					if (base_series[data] == undefined) {
						var name = data;
						console.log(data);
						var num = table_data[sample_obj][data].toString().split(" ")[0].replace(/,/g, "");
						base_series[data] = {name: name, data: [parseInt(num)]}
					}else{
						var num = table_data[sample_obj][data].toString().split(" ")[0].replace(/,/g, "");
						base_series[data]['data'].push(parseInt(num))
					}
				}
			}
		}
	}
}

function highchartSetup(sample_obj, type, categories, series){
	if (table_data[sample_obj][type] != undefined || table_data[sample_obj][type+'_unique'] != undefined) {
		categories.push(sample_obj);
		for (var data in table_data[sample_obj]) {
			var re = new RegExp(type, 'g');
			if (re.test(data)) {
				if (series[data] == undefined) {
					var name = data;
					if (data == type) {
						name = 'reads mapped'
					}else if (data == type+'_dedup') {
						name = 'dedup reads'
					}else if (data == type+'_multimap') {
						name = 'multimapped reads'
					}else if (data == type+'_unique') {
						console.log('corrrect')
						name = 'uniquely mapped reads'
					}
					var num = table_data[sample_obj][data].toString().split(" ")[0].replace(/,/g, "");
					series[data] = {name: name, data: [parseInt(num)]}
				}else{
					var num = table_data[sample_obj][data].toString().split(" ")[0].replace(/,/g, "");
					series[data]['data'].push(parseInt(num))
				}
			}
		}
		if (type == 'tophat') {
			tophat_toggle = true;
			tophat_series = series;
		}else if (type == 'chip') {
			chip_toggle = true;
			chip_series = series;
		}else if (type == 'atac') {
			atac_toggle = true;
			atac_series = series;
		}else if (type == 'star') {
			star_toggle = true;
			star_series = series;
		}else if (type == 'hisat2') {
			hisat2_toggle = true;
			hisat2_series = series;
		}
	}
}

function createSummaryHighchart(){
	if (rsem_toggle) {
		createSingleSummaryHighchart(rsem_series, rsem_categories, "rsem", "Rsem")
	}
	if (tophat_toggle) {
		createSingleSummaryHighchart(tophat_series, tophat_categories, "tophat", "Tophat")
	}
	if (chip_toggle) {
		createSingleSummaryHighchart(chip_series, chip_categories, "chip", "Chip")
	}
	if (atac_toggle) {
		createSingleSummaryHighchart(atac_series, atac_categories, "atac", "Atac")
	}
	if (star_toggle) {
		createSingleSummaryHighchart(star_series, star_categories, "star", "Star")
	}
	if (hisat2_toggle) {
		createSingleSummaryHighchart(hisat2_series, hisat2_categories, "hisat2", "Hisat2")
	}
	
	if (!chip_toggle && !tophat_toggle && !rsem_toggle && !atac_toggle && !star_toggle && !hisat2_toggle) {
		var base_final_series = [];
		for (var series in base_series) {
			base_final_series.push(base_series[series]);
		}
		createHighchart(base_categories, base_final_series, 'Distribution of Reads', 'Percentage of Reads', 'plots', 'base_plot', 'percent');
		document.getElementById('plots').appendChild(createElement('button', ['id', 'class', 'onclick', 'style'], ['base_switch', 'btn btn-primary margin', 'switchStacking("plots", "base_plot")', 'display:none']))
		document.getElementById('base_switch').innerHTML = 'Switch Plot Type';
	}
}

function createSingleSummaryHighchart(selected_series, selected_categories, lower_type, upper_type){
	var final_series = [];
	for (var series in selected_series) {
		final_series.push(selected_series[series]);
	}
	console.log(selected_series)
	createHighchart(selected_categories, final_series, 'Distribution of '+upper_type+' Reads', 'Percentage of '+upper_type, 'plots', lower_type+'_plot', 'percent');
	document.getElementById('plots').appendChild(createElement('button', ['id', 'class', 'onclick', 'style'], [lower_type+'_switch', 'btn btn-primary margin', 'switchStacking("plots", "'+lower_type+'_plot")', 'display:none']))
	document.getElementById(lower_type+'_switch').innerHTML = 'Switch '+upper_type+' Plot Type';
}

function checkTableOutput(sample_data, ui_id, row_array) {
	if (sample_data != undefined) {
		row_array.push(sample_data)
	}
	return row_array
}

function downloadInitialMapping() {
	var textString = "";
	for (var object in $('#jsontable_initial_mapping').dataTable().dataTableSettings[0].aoColumns) {
		if (object == $('#jsontable_initial_mapping').dataTable().dataTableSettings[0].aoColumns.length - 1) {
			textString += $('#jsontable_initial_mapping').dataTable().dataTableSettings[0].aoColumns[object].sTitle + "\n";
		}else{
			textString += $('#jsontable_initial_mapping').dataTable().dataTableSettings[0].aoColumns[object].sTitle + "\t";
		}
	}
	var textFile = null;
	for (var sample_array in initial_mapping_table) {
		textString += initial_mapping_table[sample_array].join("\t") + "\n";
	}
    var data = new Blob([textString], {type: 'text/tsv'});
	if (textFile !== null) {
		window.URL.revokeObjectURL(textFile);
    }
	textFile = window.URL.createObjectURL(data);
	var link = document.getElementById('download_link');
	link.href = textFile;
    link.click()
}

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	showHighchart('plots');
});

/*
 *	Page load in function
 */

$(function() {
	"use strict";
	
	/*
	 *	If current page is the reports section
	 */
	if (phpGrab.theSegment == 'report') {
		//	Initialize Variables
		var run_id = '0';
		var samples = [];
		var summary_files = [];
		var count_files = [];
		var RSEM_files = [];
		var DESEQ_files = [];
		var picard_files = [];
		var rseqc_files = [];
		var jsonparams;
		var run_status = 0;
		
		//	Gather Run ID and Samples within that run
		$.ajax({ type: "GET",
			url: BASE_PATH +"/ajax/sessionrequests.php",
			data: { p: 'getReportsRunID' },
			async: false,
			success : function(s)
			{
				var returnedSamples = s.split(',');
				for(var x = 0; x < returnedSamples.length; x++){
					if (x == 0) {
						run_id = returnedSamples[x];
					}else{
						samples.push(returnedSamples[x]);
					}
				}
			}
		});
		
		wkey = getReportWKey(run_id);
		
		//	Gather Report list Data and push to specific arrays
		$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/ngsquerydb.php",
				data: { p: 'getJSONParams', wkey: wkey },
				async: false,
				success : function(s)
				{
					jsonparams = JSON.parse(s[0].json_parameters);
					console.log(jsonparams);
					run_status = s[0].run_status;
				}
		});
		
		//	Gather Report list Data and push to specific arrays
		$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/ngsquerydb.php",
				data: { p: 'getReportList', wkey: wkey },
				async: false,
				success : function(s)
				{
					for(var x = 0; x < s.length; x++){
						if(s[x].type == 'rsem'){
							RSEM_files.push(s[x]);
						}else if (s[x].type == 'deseq'){
							DESEQ_files.push(s[x]);
						}else if (s[x].type == 'summary') {
							summary_files.push(s[x]);
						}else if (s[x].type == 'counts'){
							count_files.push(s[x]);
						}else if (s[x].type.split('_')[0] == 'picard') {
							picard_files.push(s[x]);
						}else if (s[x].type.split('_')[0] == 'RSeQC') {
							rseqc_files.push(s[x]);
						}
					}
				}
		});
		
		if (run_status == "1") {
			$.ajax({ type: "GET",
					url: BASE_PATH+"/public/ajax/ngsquerydb.php",
					data: { p: 'getSampleNames', samples: samples.toString() },
					async: false,
					success : function(s)
					{
						for(var x  = 0; x < s.length; x++){
							if (s[x].samplename == null) {
								libraries.push(s[x].name);
								dash_library.push(s[x].name.replace(/-/g,"_"));
							}else{
								libraries.push(s[x].samplename);
								dash_library.push(s[x].samplename.replace(/-/g,"_"));
							}
						}
					}
			});
			
			//	Gather read counts
			var read_counts = [];
			$.ajax({ type: "GET",
					url: BASE_PATH+"/public/ajax/initialmappingdb.php",
					data: { p: 'getCounts', samples: samples.toString() },
					async: false,
					success : function(s)
					{
						for(var x  = 0; x < s.length; x++){
							read_counts.push(s[x].total_reads);
						}
					}
			});
			
			//	Gather/organize sample data
			populateSummaryTable(summary_files);
	
			//	set up UI
			document.getElementById('jsontable_initial_mapping').appendChild(createElement('button', ['id', 'class', 'onclick'], ['initial_download_button', 'btn btn-primary margin', 'downloadInitialMapping()']))
			document.getElementById('initial_download_button').innerHTML = 'Download Initial Table';
			document.getElementById('jsontable_initial_mapping').appendChild(createElement('a', ['id', 'download', 'style'], ['download_link', 'initial_mapping.tsv', 'display:none']))
			if (summary_RNA.length > 0) {
				createDropdown(summary_RNA, 'initial_mapping');
			}
			
			//	Set up plot data
			summaryPlotSetup(table_data);
			createSummaryHighchart();
			
			console.log(initial_mapping_table)
			console.log(table_data)
			
			//Create a check for FASTQC output????
			if (getFastQCBool(run_id)) {
				createSummary(true);
				createDetails(libraries);
			}else{
				document.getElementById('summary_exp').remove();
				document.getElementById('details_exp').remove();
			}
			
			if (DESEQ_files.length > 0) {
				var deseq_file_paths = [];
				for (var z = 0; z < DESEQ_files.length; z++){
					deseq_file_paths.push(DESEQ_files[z].file);
				}
				createDropdown(deseq_file_paths, 'DESEQ');
			}else{
				document.getElementById('DESEQ_exp').remove();
			}
			
			if (RSEM_files.length > 0) {
				var rsem_file_paths = [];
				for (var z = 0; z < RSEM_files.length; z++){
					rsem_file_paths.push(RSEM_files[z].file);
				}
				createDropdown(rsem_file_paths, 'RSEM');
			}else{
				document.getElementById('RSEM_exp').remove();
			}
			
			if (picard_files.length > 0) {
				var picard_file_paths = [];
				for (var z = 0; z < picard_files.length; z++){
					picard_file_paths.push(picard_files[z].file);
				}
				createDropdown(picard_file_paths, 'picard');
			}else{
				document.getElementById('picard_exp').remove();
			}
			
			if (rseqc_files.length > 0) {
				var rseqc_file_paths = [];
				for (var z = 0; z < rseqc_files.length; z++){
					rseqc_file_paths.push(rseqc_files[z].file);
				}
				createDropdown(rseqc_file_paths, 'rseqc');
			}else{
				document.getElementById('rseqc_exp').remove();
			}
		}
		var directory = "";
		$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/initialmappingdb.php",
				data: { p: 'getDirectory', run_id: run_id.toString() },
				async: false,
				success : function(s)
				{
					console.log(s);
					directory = s[0].outdir;
				}
		});
		console.log(run_id);
		document.getElementById('back_to_adv_status').name = run_id;
	}
});
