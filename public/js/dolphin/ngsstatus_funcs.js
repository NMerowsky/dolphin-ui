	
function backToStatus(basepath){
	window.location.href = basepath + '/stat/status';
}

function sendToPlot(id){
	var wkey = getWKey(id);
	sendWKey(wkey);
	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/sessionrequests.php",
			data: { p: "setPlotToggle", type: 'normal', file: '' },
			async: true,
			success : function(s)
			{
				window.location.href = BASE_PATH+'/plot';
			}
	});
}

function sendToAdvancedStatus(run_id){
	$.ajax({ type: "GET",
		url: BASE_PATH +"/ajax/sessionrequests.php",
		data: { p: 'setAdvStatusRunID', adv_status_id: run_id },
		async: true,
		success : function(s)
		{
			window.location.href = BASE_PATH+'/stat/advstatus';
		}
	});
}

function getWKey(run_id){
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

function selectService(id, title, wkey){
	selected_service = title;
	service_id = id.split('_')[0];
	var runparams = $('#jsontable_jobs').dataTable();
	$.ajax({ type: "GET",
			 url: BASE_PATH +"/ajax/datajobs.php?id=" + service_id,
			 async: true,
			 success : function(s)
			 {
				runparams.fnClearTable();
				var parsed = s;
				var reset = "";
				for(var i = 0; i < parsed.length; i++) {
					console.log(parsed[i].title);
					if (selected_service != parsed[i].title) {
						reset = '<button id="'+parsed[i].num+'" class="btn btn-warning btn-xs pull-right" name="soft" title="Soft Reset"onclick="resetType('+run_id+', '+parsed[i].num+', \''+wkey+'\', \''+parsed[i].title+'\', \'jobs\', this)"><span class="fa fa-times"></span></button>'; 
					}else{
						reset = "";
					}
					runparams.fnAddData([
						parsed[i].title,
						parsed[i].duration,
						parsed[i].result,
						parsed[i].job_num,
						parsed[i].submit,
						parsed[i].start,
						parsed[i].finish,
						reset + 
						'<button id="'+parsed[i].num+'_select" class="btn btn-primary btn-xs pull-right" title="Select" onclick="selectJob(this.id)">&nbsp;<span class="fa fa-caret-down"></span>&nbsp;</button>'
					]);
				} // End For
				document.getElementById('service_jobs').style.display = 'inline';
				runparams.fnSort( [ [4,'asc'] ] );
				//runparams.fnAdjustColumnSizing(true);
			}
		});
	
}

function selectJob(id){
	var id_str = id.split('_')[0]
	console.log(id_str);
	$.ajax({ type: "GET",
			url: BASE_PATH + "/public/ajax/datajobout.php?id=" + id_str,
			async: false,
			success : function(s)
			{
				var parsed = s;
				joboutDataModal(parsed[0].jobname, parsed[0].jobout);
			}
		});
}

function removeServiceTable(table_str, button){
	var table = $('#jsontable_'+table_str).dataTable();
	var row = $(button).closest('tr');
	table.fnDeleteRow(row);
	table.fnDraw();
}

function errorOutModal(run_id, wkey){
	var obtained_log = "";
	$.ajax({ type: "GET",
		url: BASE_PATH +"/public/ajax/dataerrorlogs.php",
		data: { p: 'getStdOut', run_id: run_id },
		async: false,
		success : function(s)
		{
			if (s.length > 20) {
				obtained_log = "...<br>"
				for(var i = s.length - 20; i < s.length; i++){
					obtained_log += s[i];
				}
			}else{
				for(var i = 0; i < s.length; i++){
					obtained_log += s[i];
				}
			}
		}
	});
	$('#logModal').modal({
      show: true
	});
	document.getElementById('logRunId').innerHTML = "run." + run_id + ".wrapper.std:";
	document.getElementById('logDetails').innerHTML = obtained_log;
	
	if (wkey == null || wkey == "null") {
		document.getElementById('modal_adv_status').style.display = "none";
	}else{
		var adv_stat_check = [];
		$.ajax({ type: "GET",
			url: BASE_PATH + "/public/ajax/dataservice.php?wkey=" + wkey,
			async: false,
			success : function(s)
			{
				adv_stat_check = s;
			}
		});
		
		if (adv_stat_check.length > 0) {
			document.getElementById('modal_adv_status').style.display = "show";
			document.getElementById('modal_adv_status').setAttribute("onclick", "sendToAdvancedStatus("+run_id+")");
		}else{
			document.getElementById('modal_adv_status').style.display = "none";
		}
	}
}

function queueCheck(run_id){
	var run_status = [];
	$.ajax({ type: "GET",
		url: BASE_PATH +"/public/ajax/dataerrorlogs.php",
		data: { p: 'checkQueued', run_id: run_id },
		async: false,
		success : function(s)
		{
			run_status = s;
		}
	});
	
	if (run_status.length == 3) {
		if (run_status[2] == null && run_status[0] == '0' && run_status[1] == '0') {
			console.log(run_status);
		}else{
			errorOutModal(run_id, run_status[2]);
		}
	}
	
}

function runningErrorCheck(run_id){
	var run_status;
	$.ajax({ type: "GET",
		url: BASE_PATH +"/public/ajax/dataerrorlogs.php",
		data: { p: 'errorCheck', run_id: run_id },
		async: false,
		success : function(s)
		{
			run_status = s;
		}
	});
	return run_status
}

function joboutDataModal(jobname, jobout) {
   $('#joboutData').modal({
      show: true
   });
   document.getElementById('job_modal_jobname').innerHTML = jobname;
   document.getElementById('job_modal_text').innerHTML = jobout;
}
	
function changeRunPerms(id, group){
	document.getElementById('myModalPerms').innerHTML = 'Change run group';
	document.getElementById('editLabel').innerHTML = 'Which group should see this run?'
	document.getElementById('editDiv').innerHTML = '<select id="editIDSelect" class="form-control"></select>'
	document.getElementById('confirmPermsButton').setAttribute('style', 'display:show');
	document.getElementById('confirmPermsButton').setAttribute('onclick','confirmPermsChange("'+id+'")');
	document.getElementById('cancelPermsButton').innerHTML = 'Cancel';
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngsquerydb.php",
		data: { p: 'getGroups'},
		async: false,
		success : function(s)
		{
			console.log(s);
			for(var x = 0; x < s.length; x++){
				if (s[x].id == group) {
					document.getElementById('editIDSelect').innerHTML += '<option id="group_' + s[x].id + '" value="' + s[x].id + '" selected="true">' + s[x].name + '</option>';
				}else{
					document.getElementById('editIDSelect').innerHTML += '<option id="group_' + s[x].id + '" value="' + s[x].id + '">' + s[x].name + '</option>';
				}
			}
		}
	});

	var perms;
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngsquerydb.php",
		data: { p: 'getRunPerms', run_id: id.split("_")[1]},
		async: false,
		success : function(s)
		{
			console.log(s);
			perms = s;
		}	
	});
	if (perms == 3) {
		$('#only_me').iCheck('check')
	}else if (perms == 15) {
		$('#only_my_group').iCheck('check')
	}else if (perms == 32) {
		$('#everyone').iCheck('check')
	}else if (perms == 63) {
		$('#everyone').iCheck('check')
	}
	
	$('#permsModal').modal({
		show: true
	});
}

function confirmPermsChange(id){
	var perms = $('.checked')[0].children[0].value;
	var permsPassed;
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngsquerydb.php",
		data: { p: 'changeRunPerms', perms: perms, run_id: id.split("_")[1]},
		async: false,
		success : function(s)
		{
			permsPassed = s;
		}	
	});
	var group_id = document.querySelector("select").selectedOptions[0].value;
	var group_changed;
	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/ngsquerydb.php",
			data: { p: 'changeRunGroup', group_id: group_id, run_id: id.split("_")[1]},
			async: false,
			success : function(s)
			{
				group_changed = s;
			}       
    });
	$('.checked').iCheck('uncheck');
	document.getElementById('myModalGroups').innerHTML = 'Change run permissions';
	document.getElementById('confirmGroupsButton').setAttribute('style', 'display:none');
	document.getElementById('cancelGroupsButton').innerHTML = 'OK';
	if (permsPassed == 'pass' && group_changed == 'pass') {
		document.getElementById(id).setAttribute('name', group_id);
		document.getElementById('group_'+group_id).setAttribute('selected','true');
		document.getElementById('groupsLabel').innerHTML = 'Run permissions were changed!'
		document.getElementById('groupsDiv').innerHTML = '';
	}else{
		document.getElementById('groupsLabel').innerHTML = 'Error occured, run permissions were not changed.'
		document.getElementById('groupsDiv').innerHTML = '';
	}
	$('#groupsModal').modal({
			show: true
	});
}

function getRunType(){
	var runType;
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/sessionrequests.php",
		data: { p: 'getRunType' },
		async: false,
		success : function(s)
		{
			runType = s;
		}	
	});
	return runType;
}

function changeRunType(int_type){
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/sessionrequests.php",
		data: { p: 'changeRunType', run_type: int_type },
		async: false,
		success : function(s)
		{
			location.reload();
		}	
	});
}

function resetType(run_id, s_id, wkey, name, table_str, button){
	if (table_str == 'services') {
		resetService(run_id, s_id, wkey, name, button.name, table_str, button);
		if (name == "soft") {
			document.getElementById(s_id+'_select').remove();
		}
		button.name = 'hard';
		button.title = 'Hard Reset';
		button.setAttribute('class', 'btn btn-danger btn-xs pull-right');
	}else if (table_str == 'jobs') {
		resetJob(run_id, s_id, wkey, name, button.name, table_str, button);
		if (name == "soft") {
			document.getElementById(s_id+'_select').remove();
		}
		button.name = 'hard';
		button.title = 'Hard Reset';
		button.setAttribute('class', 'btn btn-danger btn-xs pull-right');
	}
}

function resetService(run_id, s_id, wkey, name, type, table_str, button){
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngs_stat_funcs.php",
		data: { p: 'resetService', run_id: run_id, wkey: wkey, s_id: s_id, name: name, type: type, clusteruser: clusteruser },
		async: false,
		success : function(s)
		{
			console.log(s);
			if (type == 'hard') {
				removeServiceTable(table_str, button);
			}
			document.getElementById('service_jobs').style.display = 'none';
		}	
	});
}

function resetJob(run_id, s_id, wkey, name, type, table_str, button){
	$.ajax({ type: "GET",
		url: BASE_PATH+"/public/ajax/ngs_stat_funcs.php",
		data: { p: 'resetJob', run_id: run_id, wkey: wkey, s_id: s_id, name: name, type: type, clusteruser: clusteruser },
		async: false,
		success : function(s)
		{
			console.log(s);
			if (type == 'hard') {
				removeServiceTable(table_str, button);
			}
		}	
	});
}
