			<div class="modal fade" id="joboutData" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <h4 class="modal-title" id="myModalLabel">Delete Run</h4>
					</div>
					<form name="editForm" role="form" method="post">
						<div class="modal-body">
							<fieldset>
								<div class="form-group">
									<label>Job Name: </label>
									<label id="job_modal_jobname"></label>
									<br>
									<p id="job_modal_text"></p>
								</div>
							</fieldset>   
						</div>
						<div class="modal-footer">
						  <button type="button" class="btn btn-default" data-dismiss="modal">Exit</button>
						</div>
					</form>
				  </div>
				</div>
			</div><!-- End Delete modal -->
			
				<section class="content-header">
					<h1>NGS Run Status<small>Advanced status details</small></h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo BASE_PATH?>"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo BASE_PATH."/search"?>">NGS Pipeline</a></li>
						<li class="active"><?php echo $field?></li
					</ol>
				</section>
				<!-- Main content -->
				<section class="content">
					<div class="row">
						<div class="col-md-11">
						<?php echo $html->sendJScript('advstatus', "", "", "", $uid, $gids); ?>
						<?php echo $html->getBasePath(BASE_PATH); ?>
						<?php echo $html->getRespBoxTable_ng("Services", "services", "<th>Name</th><th>Duration</th><th>% Complete</th><th>Start</th><th>Finish</th><th>Select</th>"); ?>
							<div id="service_jobs" style="display:none">
								<?php echo $html->getRespBoxTable_ng("Jobs", "jobs", "<th>Name</th><th>Duration</th><th>Job #</th><th>Submission Time</th><th>Start</th><th>Finish</th><th>Select</th>"); ?>
							</div>
						</div>
						<div class="col-md-11">
							<button id="back_to_status" class="btn btn-primary" onclick="backToStatus('<?php echo BASE_PATH?>')">Back to General Status</button>
						</div>
					</div><!-- /.row -->
				</section><!-- /.content -->