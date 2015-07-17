/*
 *Author: Nicholas Merowsky
 *Date: 09 Apr 2015
 *Ascription:
 */

function postInsertRunparams(json, outputdir, name, description){

   var successCheck = false;
   var runlistCheck = "";
   var runID = "";
   var barcode = 1;

   var uid = phpGrab.uid;
   var gids = phpGrab.gids;
   
   //find the run group ID
   var hrefSplit = window.location.href.split("/");
   var rerunLoc = $.inArray('rerun', hrefSplit)
   var runGroupID;
   if (rerunLoc != -1) {
       runGroupID = hrefSplit[rerunLoc+1];
   }else{
       //if not a rerun
       runGroupID = 'new';
   }

   if (json.indexOf('"barcodes":"none"') != -1) {
      barcode = 0;
   }
   
   $.ajax({
           type: 	'POST',
           url: 	BASE_PATH+'/public/ajax/ngsalterdb.php',
           data:  	{ p: "submitPipeline", json: json, outdir: outputdir, name: name, desc: description, runGroupID: runGroupID, barcode: barcode, uid: uid, gids: gids},
           async:	false,
           success: function(r)
           {
               successCheck = true;
               if (runGroupID == 'new') {
                   runlistCheck = 'insertRunlist';
                   runID = r;
               }else{
                   runlistCheck = 'insertRunlist';
                   runID = (parseInt(runGroupID) + r);
               }
           }
       });
   if (successCheck) {
       return [ runlistCheck, runID ];
   }else{
       return undefined;
   }
}

function postInsertRunlist(runlistCheck, sample_ids, runID){
   var uid = phpGrab.uid;
   var gids = phpGrab.gids;
   var successCheck = false;
       if (runlistCheck == 'insertRunlist') {
           $.ajax({
               type: 	'POST',
               url: 	BASE_PATH+'/public/ajax/ngsalterdb.php',
               data:  	{ p: runlistCheck, sampID: sample_ids, runID: runID, uid: uid, gids: gids},
               async:	false,
               success: function(r)
               {
                   successCheck = true;
               }
           });
       }
   return successCheck;
}

function deleteRunparams(run_id) {
   $('#delModal').modal({
      show: true
   });
   document.getElementById('delRunId').value = run_id;
   document.getElementById('delRunId').innerHTML = run_id;
   document.getElementById('confirm_del_btn').value = run_id;
}

function resumeSelected(run_id, groupID){
    $.ajax({ type: "POST",
		url: BASE_PATH+"/public/ajax/ngsalterdb.php",
		data: { p: "noAddedParamsRerun", run_id: run_id },
		async: false,
		success : function(s)
		{
		}
	});
    
    //   UPDATE THE PAGE
    location.reload();
}


function confirmDeleteRunparams(run_id){
   $.ajax({
            type: 	'POST',
            url: 	BASE_PATH+'/public/ajax/ngsalterdb.php',
            data:  	{ p: 'deleteRunparams', run_id: run_id },
            async:	false,
            success: function(r)
            {
               location.reload();
            }
   });
}

function killRun(run_id){
   $.ajax({
            type: 	'POST',
            url: 	BASE_PATH+'/public/ajax/kill_pid.php',
            data:  	{ p: 'killRun', run_id: run_id },
            async:	false,
            success: function(r)
            {
               console.log(r);
               //location.reload();
            }
   });
}