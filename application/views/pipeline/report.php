<!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        NGS Pipeline 
                        <small>Workflow creation</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo BASE_PATH?>"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="<?php echo BASE_PATH."/search"?>">NGS Pipeline</a></li>
			<li class="active"><?php echo $field?></li
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
		    <?php echo $html->sendJScript("report", "", "", $selection); ?>
                    <div class="row">
                        <div class="col-md-12">
                        <div class="panel panel-default">
				<div class="panel-heading">
				  <h4>Analysis Results <small>Comprehensive Analysis</small></h4>
				</div>
				<div class="panel-body">
				    <?php echo $html->getExpandingAnalysisBox('Initial Mapping Results', "initial_mapping", true); ?>
				    <?php echo $html->getExpandingAnalysisBox('Summary', "summary", false); ?>
				    <?php echo $html->getExpandingAnalysisBox('Detailed Results', "details", false); ?>
				</div>
			</div>
                        </div><!-- /.col (RIGHT) -->
                    </div><!-- /.row -->
                    <div class="row">

                    </div><!-- /.row -->
                </section><!-- /.content -->