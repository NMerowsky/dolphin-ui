			<section class="content-header">
				<h1>
					NGS Browser
					<small>Project and experiment search</small>
				</h1>
				<ol class="breadcrumb">
					<li><a href="<?php echo BASE_PATH?>"><i class="fa fa-dashboard"></i> Home</a></li>
					<li><a href="<?php echo BASE_PATH."/search"?>">NGS Browser</a></li>
					<li class="active"><?php echo $field?></li>
				</ol>
			</section>
			<!-- Main content -->
			<section class="content">
				<div class="row">
						<?php
							echo $html->encodeDetailsBox('Donor Information', 'donor_info', ['id'], ['id']);
							echo $html->encodeDetailsBox('Sample Information', 'sample_info', ['id'], ['id']);
							echo $html->encodeDetailsBox('Treatment Information', 'treatment_info', ['id'], ['id']);
							echo $html->encodeDetailsBox('Antibody Lot Information', 'antibodby_lot_info', ['id'], ['id']);
							echo $html->encodeDetailsBox('File Information', 'file_info', ['id'], ['id']);
							echo $html->resubmitEncodeBtn("encode_resub", 4);
						?>
				</div>
			</section>
			<!-- End Main content -->
