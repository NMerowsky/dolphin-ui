		<!-- Content Header (Page header) -->
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
                        <div class="col-md-3">

              <div class="box box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Browse</h3>
		    <div class="panel-tools pull-right">
			<button class="btn btn-primary" onclick="returnToIndex();">
				<i class="fa fa-fast-backward"></i>
			</button>
		    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <div class="box-group" id="accordion">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                    <?php echo $html->getAccordion("Assay", $assay, $search)?>
		    <?php echo $html->getAccordion("Organism", $organism, $search)?>
		    <?php echo $html->getAccordion("Molecule", $molecule, $search)?>
		    <?php echo $html->getAccordion("Source", $source, $search)?>
		    <?php echo $html->getAccordion("Genotype", $genotype, $search)?>
                  </div>
                </div><!-- /.box-body -->
		</div><!-- /.box -->
		<div>
		    <?php echo $html->getDolphinBasket()?>
		</div>
          <!-- END ACCORDION & CAROUSEL-->
			</div><!-- /.col (LEFT) -->
                        <div class="col-md-9">
			    <?php
			    echo $html->sendJScript($segment, $table, $value, $search);
			    if ($table!=""){
				echo $html->getBrowserPanel($experiment_series, $experiment_series_fields, "Experiment Series", 'experiment_name');
			    }
			    else{
			        echo $html->getRespBoxTable_ng("Experiment Series", "experiment_series", "<th>id</th><th>Name</th><th>Summary</th><th>Design</th>");
			    }
			    if ($table=="experiments" || $table=="samples"){
                                echo $html->getBrowserPanel($experiments, $experiment_fields, "Experiment", 'name');
			    }
			    else{
			       echo $html->getRespBoxTable_ng("Experiments", "lanes", "<th>id</th><th>Experiment name</th><th>Facility</th><th>Total Reads</th><th>Total Samples</th><th>Selected</th>");
			    }
			    if ($table=="samples"){
				echo $html->getBrowserPanel($samples, $sample_fields, "Sample",'name');
				//echo $html->getQCPanel();
				//echo $html->getRSEMPanel();
				//echo $html->getDESeqPanel();
				//echo $html->getTophatPanel();
				
			    }
			    else{
                              echo $html->getRespBoxTable_ng("Samples", "samples", "<th>id</th><th>Title</th><th>Source</th><th>Organism</th><th>Molecule</th><th>Selected</th>");
			      echo $html->getSubmitBrowserButton();
			    }
			    ?>
                        </div><!-- /.col (RIGHT) -->
                    </div><!-- /.row -->
                </section><!-- /.content -->
                
                

