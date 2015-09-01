/*
 *Author: Nicholas Merowsky
 *Date: 09 Apr 2015
 *Ascription:
 */

var element_highlighted;
var element_highlighted_table;
var element_highlighted_value;
var element_highlighted_id;
var element_highlighted_type;
var element_highlighted_onclick;

var experimentPerms = [];
var lanePerms = [];
var samplePerms = [];

var normalized = ['facility', 'source', 'organism', 'molecule', 'lab', 'organization', 'genotype', 'library_type',
				  'biosample_type', 'instrument_model', 'treatment_manufacturer'];

function editBox(uid, id, type, table, element){
	
	var havePermission = 0;
	
	$.ajax({ type: "GET",
					url: BASE_PATH+"/public/ajax/browse_edit.php",
					data: { p: 'checkPerms', id: id, uid: uid, table: table},
					async: false,
					success : function(r)
					{
						havePermission = r;
					}
				});
	
	if (havePermission == 1) {
		if (element_highlighted != null) {
			element_highlighted.innerHTML = element_highlighted_value;
			element_highlighted.onclick = element_highlighted_onclick;
		}
		element_highlighted = element;
		element_highlighted_value = element.innerHTML;
		element_highlighted_id = id;
		element_highlighted_type = type;
		element_highlighted_table = table;
		element_highlighted_onclick = element.onclick;
		element.innerHTML = '';
		
		if (normalized.indexOf(type) > -1) {
			
			element.onclick = '';
			
			var masterDiv = document.createElement('div');
			masterDiv.setAttribute('class','combobox input-group');
			
			var input = document.createElement('input');
			input.setAttribute('type', 'text');
			input.setAttribute('name', 'comboboxfieldname');
			input.setAttribute('id','cb_identifier');
			input.setAttribute('class','cb_identifier');
			input.value = element_highlighted_value;
			
			var button = document.createElement('button');
			button.setAttribute('class', 'btn btn-default btn-group pull-right')
			
			var span = document.createElement('span');
			span.setAttribute('class','caret');
			button.appendChild(span);
			
			var div = document.createElement('div');
			div.setAttribute('class','dropdownlist');
			div.setAttribute('style','z-index: 999');
			
			$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/browse_edit.php",
				data: { p: 'getDropdownValues', type: type},
				async: false,
				success : function(r)
				{
					for(var x = 0; x < r.length; x++){
						var a = document.createElement('a');
						a.innerHTML = (r[x][type]);
						div.appendChild(a);
					}
				}
			});
			
			element.appendChild(masterDiv);
			masterDiv.appendChild(input);
			masterDiv.appendChild(button);
			masterDiv.appendChild(div);
			var no = new ComboBox('cb_identifier');
			
		}else{
			var textarea = document.createElement('textarea');
			textarea.setAttribute('type', 'text');
			textarea.setAttribute('class', 'form-control');
			textarea.setAttribute('rows', '5');
			textarea.setAttribute('onkeydown', 'submitChanges(this)');
			
			element.setAttribute('value', element_highlighted_value);
			element.appendChild(textarea);
			textarea.innerHTML = element_highlighted_value;
			element.onclick = '';
		}
	}
}

function submitChanges(ele) {
	var successBool = false;
    if(event.keyCode == 13 && ele.value != '' && ele.value != null) {
        $.ajax({ type: "GET",
					url: BASE_PATH+"/public/ajax/browse_edit.php",
					data: { p: 'updateDatabase', id: element_highlighted_id, type: element_highlighted_type, table: element_highlighted_table, value: ele.value},
					async: false,
					success : function(r)
					{
						if (r == 1) {
							successBool = true;
						}
					}
				});
		if (successBool) {
			element_highlighted.innerHTML = ele.value;
			element_highlighted.onclick = element_highlighted_onclick;
			
			clearElementHighlighted();
		}
    }else if(event.keyCode == 27) {
		element_highlighted.innerHTML = element_highlighted_value;
		element_highlighted.onclick = element_highlighted_onclick;
		
		clearElementHighlighted();
	}
}

function deleteButton(){
	$('#deleteModal').modal({
		show: true
	});
	
	if (checklist_samples.length == 0){
		document.getElementById('myModalLabel').innerHTML = 'Delete Error';
		document.getElementById('deleteLabel').innerHTML = 'You must select samples to delete to continue.';
		
		document.getElementById('cancelDeleteButton').innerHTML = "OK";
		document.getElementById('confirmDeleteButton').setAttribute('style', 'display:none');
	}else if (checklist_experiment_series.length > 0) {
		document.getElementById('myModalLabel').innerHTML = 'Delete Experiment Series';
		document.getElementById('deleteLabel').innerHTML = 'Warning!  You have selected to remove an experiment series!';
		document.getElementById('deleteAreas').innerHTML = 'Are you sure you want to continue?<br>'+'Experiment series: '+checklist_experiment_series.toString();
		
		document.getElementById('confirmDeleteButton').setAttribute('onclick', 'deletePermsModal()');
		document.getElementById('confirmDeleteButton').setAttribute('data-dismiss', '');
		document.getElementById('confirmDeleteButton').setAttribute('style', 'display:show');
	}else{
		deletePermsModal();
	}
}

function deletePermsModal(){
	experimentPerms = [];
	lanePerms = [];
	samplePerms = [];	

	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/browse_edit.php",
			data: { p: 'getExperimentPermissions', experiments: checklist_experiment_series.toString() },
			async: false,
			success : function(s)
			{
				for(var x = 0; x < s.length; x++){
					experimentPerms.push(s[x].id);
				}
			}
	});
	
	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/browse_edit.php",
			data: { p: 'getLanePermissions', lanes: checklist_lanes.toString() },
			async: false,
			success : function(s)
			{
				for(var x = 0; x < s.length; x++){
					lanePerms.push(s[x].id);
				}
			}
	});
	
	$.ajax({ type: "GET",
			url: BASE_PATH+"/public/ajax/browse_edit.php",
			data: { p: 'getSamplePermissions', samples: checklist_samples.toString() },
			async: false,
			success : function(s)
			{
				for(var x = 0; x < s.length; x++){
					samplePerms.push(s[x].id);
				}
			}
	});
	
	var badExperiments = [];
	var badLanes = [];
	var badSamples = [];
	for (var q = 0; q < checklist_experiment_series.length; q++) {
		if (experimentPerms.indexOf(checklist_experiment_series[q]) == -1) {
			badExperiments.push(checklist_experiment_series[q]);
		}
	}
	for (var q = 0; q < checklist_lanes.length; q++) {
		if (lanePerms.indexOf(checklist_lanes[q]) == -1) {
			badLanes.push(checklist_lanes[q]);
		}
	}
	for (var q = 0; q < checklist_samples.length; q++) {
		if (samplePerms.indexOf(checklist_samples[q]) == -1) {
			badSamples.push(checklist_samples[q]);
		}
	}
	
	document.getElementById('myModalLabel').innerHTML = 'Delete Selected';
	document.getElementById('deleteLabel').innerHTML ='You have permission to delete the following:';
	document.getElementById('deleteAreas').innerHTML = 'Experiment Series: ' + experimentPerms.join(", ") + '<br>Imports: '+ lanePerms.join(", ") + '<br>Samples: ' + samplePerms.join(", ") +
		'<br><br>Experiment Series lacking permissions: ' + badExperiments.join(", ") + '<br>Imports lacking permissions: ' + badLanes.join(", ") + '<br>Samples lacking permissions: ' + badSamples.join(", ") +
		'<br><br>If the Import or Sample you want to delete is not accessible, you do not have the correct permissions to remove them.'+
		'<br><br>Be Warned! Deleting Imports/Samples will remove data AND runs accross the system, make sure you have a back up of any of the information you might want to save before deleting.'+
		'<br><br>Data is not recoverable, please make sure you want to delete these.';
		
	document.getElementById('cancelDeleteButton').innerHTML = "Cancel";
	document.getElementById('confirmDeleteButton').setAttribute('style', 'display:show');
	document.getElementById('confirmDeleteButton').setAttribute('onclick', 'confirmDeletePressed()');
}

function confirmDeletePressed(){
		$.ajax({ type: "GET",
				url: BASE_PATH+"/public/ajax/browse_edit.php",
				data: { p: 'deleteSelected', samples: samplePerms.toString(), lanes: lanePerms.toString(), experiments: experimentPerms.toString() },
				async: false,
				success : function(s)
				{
				}
		});
		
		experimentPerms = [];
		lanePerms = [];
		samplePerms = [];
		
		flushBasketInfo();
		
		location.reload();
	}

function cancelDeletePressed(){ 
	experimentPerms = [];
	lanePerms = [];
	samplePerms = [];
}

function initialRunButton(type, id, button){
	$('#deleteModal').modal({
		show: true
	});
	document.getElementById('myModalLabel').innerHTML = 'Information';
	document.getElementById('deleteLabel').innerHTML ='Initial run has not completed yet.';
	document.getElementById('deleteAreas').innerHTML = 'If Import/Sample seems to be taking too long to complete, please contact an Admin at biocore@umassmed.edu';
	
	document.getElementById('cancelDeleteButton').innerHTML = "OK";
	document.getElementById('confirmDeleteButton').setAttribute('style', 'display:none');
}

function clearElementHighlighted(){
	element_highlighted = null;
	element_highlighted_table = null;
	element_highlighted_value = null;
	element_highlighted_id = null;
	element_highlighted_type = null;
	element_highlighted_onclick = null;
}