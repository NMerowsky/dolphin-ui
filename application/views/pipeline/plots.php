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
		    <?php echo $html->sendJScript("plots", "", "", $selection); ?>
                    <div class="row">
                        <div class="col-md-12">
                        <div class="panel panel-default">
				<div class="panel-heading">
				  <h4>Plot Analysis<small>Interactive Analysis</small></h4>
				</div>
				<div class="panel-body">
				    <?php echo $html->getExpandingAnalysisBox('Plots', "plots", false); ?>
				</div>
			</div>
                        </div><!-- /.col (RIGHT) -->
                    </div><!-- /.row -->
                    <div class="row">

                    </div><!-- /.row -->
                </section><!-- /.content -->