/*
 *Author: Nicholas Merowsky
 *Date: 09 Apr 2015
 *Ascription:
 */

function rerunSelected(runID, groupID){
	var sample_ids = [];
	$.ajax({ type: "GET",
		 url: "/dolphin/public/ajax/ngsquerydb.php",
		 data: { p: "getRunSamples", runID: runID },
		 async: false,
		 success : function(s)
		 {
			for(var i = 0; i < s.length; i++) {
			sample_ids.push(s[i].sample_id);
			}
		 }
	});
	window.location.href = "/dolphin/pipeline/rerun/" + runID + "/" + sample_ids + "$";
}

function reportSelected(runID, groupID){
	var ids = [];
	$.ajax({ type: "GET",
		 url: "/dolphin/public/ajax/ngsquerydb.php",
		 data: { p: "getRunSamples", runID: runID },
		 async: false,
		 success : function(s)
		 {
			for(var i = 0; i < s.length; i++) {
			ids.push(s[i].sample_id);
			}
		 }
	});
	window.location.href = "/dolphin/pipeline/report/" + runID + '/' + ids + "$";
}

function getSampleIDs(search){
	var ids= [];
	$.ajax({ type: "GET",
		 url: "/dolphin/public/ajax/ngsquerydb.php",
		 data: { p: "getSelectedSamples", search: search },
		 async: false,
		 success : function(s)
		 {
			for(var i = 0; i < s.length; i++) {
			ids.push(s[i].id);
			}
		 }
	});
	return ids;
}

function grabReload(groupID){
	jsonArray = [];
	$.ajax({ type: "GET",
		 url: "/dolphin/public/ajax/ngsquerydb.php",
		 data: { p: "grabReload", groupID: groupID },
		 async: false,
		 success : function(s)
		 {
					var jsonObj = JSON.parse(s[0].json_parameters);
			jsonArray.push(jsonObj);
			jsonArray.push(s[0].outdir);
			jsonArray.push(s[0].run_name);
			jsonArray.push(s[0].run_description);
		 }
	});
	return jsonArray
}

function getSummaryInfo(runid, sampleids){
	var nameArray = [];
	var dirArray = [];
	$.ajax({ type: "GET",
		url: "/dolphin/public/ajax/ngsquerydb.php",
		data: { p: "getReportNames", runid: runid, samp: sampleids },
		async: false,
		success : function(s)
		{
			for(var x = 0; x < s.length; x++){
				nameArray.push(s[x].file_name);
				dirArray.push(s[x].outdir);
			}
		}
	});
	return [nameArray, dirArray];
}

function getLanesToSamples(lane_id){
	samples_returned = [];
	$.ajax({ type: "GET",
		url: "/dolphin/public/ajax/ngsquerydb.php",
		data: { p: "lanesToSamples", lane: lane_id},
		async: false,
		success : function(s)
		{
			for(var x = 0; x < s.length; x++){
				samples_returned.push(s[x].id);
			}
		}
	});
	return samples_returned;
}

function getAllSampleIds(){
	samples_returned = [];
	$.ajax({ type: "GET",
		url: "/dolphin/public/ajax/ngsquerydb.php",
		data: { p: "getAllSampleIds" },
		async: false,
		success : function(s)
		{
			for(var x = 0; x < s.length; x++){
				samples_returned.push(s[x].id);
			}
		}
	});
	return samples_returned;
}

function getAllLaneIds(){
	lanes_returned = [];
	$.ajax({ type: "GET",
		url: "/dolphin/public/ajax/ngsquerydb.php",
		data: { p: "getAllLaneIds" },
		async: false,
		success : function(s)
		{
			for(var x = 0; x < s.length; x++){
				lanes_returned.push(s[x].id);
			}
		}
	});
	return lanes_returned;
}

function getSeriesIdFromLane(lane){
	series_id_returned = -1;
	$.ajax({ type: "GET",
		url: "/dolphin/public/ajax/ngsquerydb.php",
		data: { p: "getSeriesIdFromLane", lane: lane },
		async: false,
		success : function(s)
		{
			series_id_returned = s[0].series_id;
		}
	});
	return series_id_returned;
}

function getLaneIdFromSample(sample){
	lane_id_returned = -1;
	$.ajax({ type: "GET",
		url: "/dolphin/public/ajax/ngsquerydb.php",
		data: { p: "getLaneIdFromSample", sample: sample },
		async: false,
		success : function(s)
		{
			if (s[0] != undefined) {
				lane_id_returned = s[0].id;
			}
		}
	});
	return lane_id_returned;
}

function getSingleSample(sampleID){
	var sample_info = [];
	$.ajax({ type: "GET",
		url: "/dolphin/public/ajax/ngsquerydb.php",
		data: { p: "getSingleSample", sample: sampleID },
		async: false,
		success : function(s)
		{
			if (s[0] != undefined) {
				sample_info.push(s[0].id);
				sample_info.push(s[0].title);
			}
		}
	});
	return sample_info;
}

function findIfMatePaired(runID){
    var checkMP = false;
    $.ajax({ type: "GET",
		url: "/dolphin/public/ajax/ngsquerydb.php",
		data: { p: "checkMatePaired", runid: runID },
		async: false,
		success : function(s)
		{
            var jsonGrab = s.map(JSON.stringify);
            newJsonArray = jsonGrab[0].split('\\"');
			if (newJsonArray[7] == 'paired') {
                checkMP = true;
            }
		}
	});
    return checkMP;
}
