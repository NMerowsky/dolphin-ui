/*
 * Author: Alper Kucukural
 * Co-Editor: Nicholas Merowsky
 * Date: 26 Nov 2014
 * Ascription:
 **/

$(function() {
	"use strict";

	//Rerun Check
	rerunLoad();

	//The Calender
	$("#calendar").datepicker();

	/*##### PAGE DETERMINER #####*/

	var qvar = "";
	var rvar = "";
	var segment = "";
	var theSearch = "";

	if (phpGrab) {
	var segment = phpGrab.theSegment;
	var theSearch = phpGrab.theSearch;
	}

	//Details values
	if (segment == "details") {
	if (phpGrab.theField == "experiment_series") {
		qvar = phpGrab.theValue;
	}
	else if (phpGrab.theField == "experiments") {
		rvar = phpGrab.theValue;
	}
	}

	//Browse values
	else if (segment == "browse") {
	qvar = phpGrab.theField;//field
	rvar = unescape(phpGrab.theValue);//value
	}

	if (phpGrab.theField == "samples") {
	reloadBasket();
	}

	/*##### STATUS TABLE #####*/
	if (segment == 'status') {
	var runparams = $('#jsontable_runparams').dataTable();

	$.ajax({ type: "GET",
			 url: "/dolphin/public/ajax/ngsquerydb.php",
			 data: { p: "getStatus", q: qvar, r: rvar, seg: segment, search: theSearch },
			 async: false,
			 success : function(s)
			 {
				runparams.fnClearTable();
				for(var i = 0; i < s.length; i++) {
				var runstat = "";
				if (s[i].run_status == 0) {
				runstat = '<button type="button" class="btn btn-xs disabled"><i class="fa fa-refresh">\tRunning...</i></button>';
				}else if (s[i].run_status == 1) {
				runstat = '<button type="button" class="btn btn-success btn-xs disabled"><i class="fa fa-check">\tComplete!</i></button>';
				}else{
				runstat = '<button type="button" class="btn btn-danger btn-xs disabled"><i class="fa fa-warning">\tError</i></button>';
				}
				runparams.fnAddData([
				s[i].id,
				s[i].run_group_id,
				s[i].run_name,
				s[i].outdir,
				s[i].run_description,
				runstat,
				'<div class="btn-group">' +
				'<input type="button" id="'+s[i].id+'" name="'+s[i].run_group_id+'" class="btn btn-xs btn-primary" value="Report Details" onClick="reportSelected(this.id, this.name)"/>' +
				'<input type="button" id="'+s[i].id+'" name="'+s[i].run_group_id+'" class="btn btn-xs btn-primary disabled" value="Pause" onClick=""/>' +
				'<input type="button" id="'+s[i].id+'" name="'+s[i].run_group_id+'" class="btn btn-xs btn-primary" value="Re-run" onClick="rerunSelected(this.id, this.name)"/>' +
				'</div>',
				]);
				} // End For
			}
		});

	$('.daterange_status').daterangepicker(
		{
			ranges: {
			'Today': [moment().subtract('days', 1), moment()],
			'Yesterday': [moment().subtract('days', 2), moment().subtract('days', 1)],
			'Last 7 Days': [moment().subtract('days', 6), moment()],
			'Last 30 Days': [moment().subtract('days', 29), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
			'This Year': [moment().startOf('year'), moment().endOf('year')],
			},
			startDate: moment().subtract('days', 29),
			endDate: moment()
		},
	function(start, end) {
		$.ajax({ type: "GET",
			 url: "/dolphin/public/ajax/ngsquerydb.php",
			 data: { p: "getStatus", q: qvar, r: rvar, seg: segment, search: theSearch, start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
			 async: false,
			 success : function(s)
			 {
				runparams.fnClearTable();
				for(var i = 0; i < s.length; i++) {
				var runstat = "";
				if (s[i].run_status == 0) {
				runstat = '<button type="button" class="btn btn-xs disabled"><i class="fa fa-refresh">\tRunning...</i></button>';
				}else if (s[i].run_status == 1) {
				runstat = '<button type="button" class="btn btn-success btn-xs disabled"><i class="fa fa-check">\tComplete!</i></button>';
				}else{
				runstat = '<button type="button" class="btn btn-danger btn-xs disabled"><i class="fa fa-warning">\tError</i></button>';
				}
				runparams.fnAddData([
				s[i].id,
				s[i].run_group_id,
				s[i].run_name,
				s[i].outdir,
				s[i].run_description,
				runstat,
				'<div class="btn-group">' +
				'<input type="button" id="'+s[i].id+'" name="'+s[i].run_group_id+'" class="btn btn-xs btn-primary" value="Report Details" onClick="reportSelected(this.id, this.name)"/>' +
				'<input type="button" id="'+s[i].id+'" name="'+s[i].run_group_id+'" class="btn btn-xs btn-primary disabled" value="Pause" onClick=""/>' +
				'<input type="button" id="'+s[i].id+'" name="'+s[i].run_group_id+'" class="btn btn-xs btn-primary" value="Re-run" onClick="rerunSelected(this.id, this.name)"/>' +
				'</div>',
				]);
				} // End For
			 }
		});

	});

	runparams.fnSort( [ [0,'des'] ] );
	runparams.fnAdjustColumnSizing(true);
	}


	/*##### PROTOCOLS TABLE #####*/

	var protocolsTable = $('#jsontable_protocols').dataTable();

	 $.ajax({ type: "GET",
					 url: "/dolphin/public/ajax/ngsquerydb.php",
					 data: { p: "getProtocols", type:"Dolphin", q: qvar, r: rvar, seg: segment, search: theSearch},
					 async: false,
					 success : function(s)
					 {
						protocolsTable.fnClearTable();
						for(var i = 0; i < s.length; i++) {
						protocolsTable.fnAddData([
			s[i].id,
			"<a href=\"/dolphin/search/details/protocols/"+s[i].id+'/'+theSearch+"\">"+s[i].name+"</a>",
						s[i].growth,
			s[i].treatment,
						]);
						} // End For
					 }
			});

	$('.daterange_protocols').daterangepicker(
			{
				ranges: {
					'Today': [moment().subtract('days', 1), moment()],
					'Yesterday': [moment().subtract('days', 2), moment().subtract('days', 1)],
					'Last 7 Days': [moment().subtract('days', 6), moment()],
					'Last 30 Days': [moment().subtract('days', 29), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
					'This Year': [moment().startOf('year'), moment().endOf('year')],
				},
				startDate: moment().subtract('days', 29),
				endDate: moment()
			},
	function(start, end) {
			$.ajax({ type: "GET",
					 url: "/dolphin/public/ajax/ngsquerydb.php",
					 data: { p: "getProtocols", q: qvar, r: rvar, seg: segment, search: theSearch, start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
					 async: false,
					 success : function(s)
					 {
						protocolsTable.fnClearTable();
						for(var i = 0; i < s.length; i++) {
						protocolsTable.fnAddData([
			s[i].id,
			"<a href=\"/dolphin/search/details/protocols/"+s[i].id+'/'+theSearch+"\">"+s[i].name+"</a>",
						s[i].growth,
			s[i].treatment,
						]);
						} // End For
					 }
			});

	});
	protocolsTable.fnSort( [ [0,'asc'] ] );
	//protocolsTable.fnAdjustColumnSizing(true);

	/*##### SAMPLES TABLE #####*/

	var samplesTable = $('#jsontable_samples').dataTable();

	var samplesType = "";
	if (segment == 'selected') {
		samplesType = "getSelectedSamples";
	}
	else{
		samplesType = "getSamples";
	}
	$.ajax({ type: "GET",
					 url: "/dolphin/public/ajax/ngsquerydb.php",
					 data: { p: samplesType, q: qvar, r: rvar, seg: segment, search: theSearch },
					 async: false,
					 success : function(s)
					 {
						samplesTable.fnClearTable();
						var changeHTML = '';
						var hrefSplit = window.location.href.split("/");
						var typeLocSelected = $.inArray('selected', hrefSplit);
						var typeLocRerun = $.inArray('rerun', hrefSplit);
						if (typeLocSelected > 0 || typeLocRerun > 0) {
							theSearch = '';
						}
						for(var i = 0; i < s.length; i++) {
						samplesTable.fnAddData([
						s[i].id,
			"<a href=\"/dolphin/search/details/samples/"+s[i].id+'/'+theSearch+"\">"+s[i].title+"</a>",
			s[i].source,
			s[i].organism,
			s[i].molecule,
			"<input type=\"checkbox\" class=\"ngs_checkbox\" name=\""+s[i].id+"\" id=\"sample_checkbox_"+s[i].id+"\" onClick=\"manageChecklists(this.name, 'sample_checkbox');\">",
						]);
						} // End For
					 }
			});

	$('.daterange_samples').daterangepicker(
			{
				ranges: {
					'Today': [moment().subtract('days', 1), moment()],
					'Yesterday': [moment().subtract('days', 2), moment().subtract('days', 1)],
					'Last 7 Days': [moment().subtract('days', 6), moment()],
					'Last 30 Days': [moment().subtract('days', 29), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
					'This Year': [moment().startOf('year'), moment().endOf('year')],
				},
				startDate: moment().subtract('days', 29),
				endDate: moment()
			},
	function(start, end) {
			$.ajax({ type: "GET",
					 url: "/dolphin/public/ajax/ngsquerydb.php",
					 data: { p: samplesType, q: qvar, r: rvar, seg: segment, search: theSearch, start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
					 async: false,
					 success : function(s)
					 {
						samplesTable.fnClearTable();
						var changeHTML = '';
						var hrefSplit = window.location.href.split("/");
						var typeLocSelected = $.inArray('selected', hrefSplit);
						var typeLocRerun = $.inArray('rerun', hrefSplit);
						if (typeLocSelected > 0 || typeLocRerun > 0) {
							theSearch = '';
						}
						for(var i = 0; i < s.length; i++) {
						samplesTable.fnAddData([
						s[i].id,
			"<a href=\"/dolphin/search/details/samples/"+s[i].id+'/'+theSearch+"\">"+s[i].title+"</a>",
			s[i].source,
			s[i].organism,
			s[i].molecule,
			"<input type=\"checkbox\" class=\"ngs_checkbox\" name=\""+s[i].id+"\" id=\"sample_checkbox_"+s[i].id+"\" onClick=\"manageChecklists(this.name, 'sample_checkbox');\">",
						]);
						} // End For
					 }
			});

	});

	samplesTable.fnSort( [ [0,'asc'] ] );
	samplesTable.fnAdjustColumnSizing(true);

	if (phpGrab.theField == "experiments") {
	checkOffAllSamples();
	reloadBasket();
	}

	/*##### LANES TABLE #####*/

	var lanesTable = $('#jsontable_lanes').dataTable();

	$.ajax({ type: "GET",
					 url: "/dolphin/public/ajax/ngsquerydb.php",
					 data: { p: "getLanes", q: qvar, r: rvar, seg: segment, search: theSearch },
					 async: false,
					 success : function(s)
					 {
						lanesTable.fnClearTable();
						for(var i = 0; i < s.length; i++) {
						lanesTable.fnAddData([
						s[i].id,
			"<a href=\"/dolphin/search/details/experiments/"+s[i].id+'/'+theSearch+"\">"+s[i].name+"</a>",
			s[i].facility,
			s[i].total_reads,
			s[i].total_samples,
			"<input type=\"checkbox\" class=\"ngs_checkbox\" name=\""+s[i].id+"\" id=\"lane_checkbox_"+s[i].id+"\" onClick=\"manageChecklists(this.name, 'lane_checkbox');\">",
						]);
						} // End For
					}
			});

	$('.daterange_lanes').daterangepicker(
			{
				ranges: {
					'Today': [moment().subtract('days', 1), moment()],
					'Yesterday': [moment().subtract('days', 2), moment().subtract('days', 1)],
					'Last 7 Days': [moment().subtract('days', 6), moment()],
					'Last 30 Days': [moment().subtract('days', 29), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
					'This Year': [moment().startOf('year'), moment().endOf('year')],
				},
				startDate: moment().subtract('days', 29),
				endDate: moment()
			},
	function(start, end) {
			$.ajax({ type: "GET",
					 url: "/dolphin/public/ajax/ngsquerydb.php",
					 data: { p: "getLanes", q: qvar, r: rvar, seg: segment, search: theSearch, start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
					 async: false,
					 success : function(s)
					 {
						lanesTable.fnClearTable();
						for(var i = 0; i < s.length; i++) {
						lanesTable.fnAddData([
						s[i].id,
			"<a href=\"/dolphin/search/details/experiments/"+s[i].id+'/'+theSearch+"\">"+s[i].name+"</a>",
			s[i].facility,
			s[i].total_reads,
			s[i].total_samples,
			"<input type=\"checkbox\" class=\"ngs_checkbox\" name=\""+s[i].id+"\" id=\"lane_checkbox_"+s[i].id+"\" onClick=\"manageChecklists(this.name, 'lane_checkbox');\">",
						]);
						} // End For
					 }
			});

	});

	lanesTable.fnSort( [ [0,'asc'] ] );
	lanesTable.fnAdjustColumnSizing(true);

	if (phpGrab.theField == "experiment_series") {
	checkOffAllSamples();
	checkOffAllLanes();
	reloadBasket();
	}

	/*##### SERIES TABLE #####*/

	 var experiment_seriesTable = $('#jsontable_experiment_series').dataTable();
	 $.ajax({ type: "GET",
					 url: "/dolphin/public/ajax/ngsquerydb.php",
					 data: { p: "getExperimentSeries", q: qvar, r: rvar, seg: segment, search: theSearch },
					 async: false,
					 success : function(s)
					 {
						experiment_seriesTable.fnClearTable();
						for(var i = 0; i < s.length; i++) {
						experiment_seriesTable.fnAddData([
			s[i].id,
			"<a href=\"/dolphin/search/details/experiment_series/"+s[i].id+'/'+theSearch+"\">"+s[i].experiment_name+"</a>",
						s[i].summary,
						s[i].design,
						]);
						} // End For
					 }
			});

	 $('.daterange_experiment_series').daterangepicker(
			{
				ranges: {
					'Today': [moment().subtract('days', 1), moment()],
					'Yesterday': [moment().subtract('days', 2), moment().subtract('days', 1)],
					'Last 7 Days': [moment().subtract('days', 6), moment()],
					'Last 30 Days': [moment().subtract('days', 29), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
					'This Year': [moment().startOf('year'), moment().endOf('year')],
				},
				startDate: moment().subtract('days', 29),
				endDate: moment()
			},
	function(start, end) {
			$.ajax({ type: "GET",
					 url: "/dolphin/public/ajax/ngsquerydb.php",
					 data: { p: "getExperimentSeries", q: qvar, r: rvar, seg: segment, search: theSearch, start:start.format('YYYY-MM-DD'), end:end.format('YYYY-MM-DD') },
					 async: false,
					 success : function(s)
					 {
						experiment_seriesTable.fnClearTable();
						for(var i = 0; i < s.length; i++) {
						experiment_seriesTable.fnAddData([
			s[i].id,
			"<a href=\"/dolphin/search/details/experiment_series/"+s[i].id+'/'+theSearch+"\">"+s[i].experiment_name+"</a>",
						s[i].summary,
						s[i].design,
						]);
						} // End For
					 }
			});

	});

	experiment_seriesTable.fnSort( [ [0,'asc'] ] );
	experiment_seriesTable.fnAdjustColumnSizing(true);

	checkOffAllSamples();
	checkOffAllLanes();
	reloadBasket();
});




