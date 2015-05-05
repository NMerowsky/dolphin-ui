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
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo $html->getRespBoxTable_ng("Samples Selected", "samples", "<th>id</th><th>Title</th><th>Source</th><th>Organism</th><th>Molecule</th>"); ?>
                        </div><!-- /.col (RIGHT) -->
                    </div><!-- /.row -->
                    <div class="row">
			<?php echo $html->sendJScript("selected", "", "", $selection); ?>
                        <?php echo $html->getStaticSelectionBox("Name the Run", "run_name", "TEXT", 4)?>
			<?php echo $html->getStaticSelectionBox("Description", "description", "TEXT", 8)?>
			<?php echo $html->getStaticSelectionBox("Genome Build", "genomebuild", "<option>human,hg19</option>
                                                                                <option>hamster,cho-k1</option>
                                                                                <option>rat,rn5</option>
                                                                                <option>zebrafish,danrer7</option>
                                                                                <option>mouse,mm10</option>
                                                                                <option>mousetest,mm10</option>
                                                                                <option>s_cerevisiae,saccer3</option>
                                                                                <option>c_elegans,ce10</option>
                                                                                <option>cow,bostau7</option>
                                                                                <option>d_melanogaster,dm3</option>", 4)?>
                        <?php echo $html->getStaticSelectionBox("Mate-paired", "spaired", "<option>yes</option>
                                                                                <option>no</option>", 4)?>
                        <?php echo $html->getStaticSelectionBox("Fresh Run", "resume", "<option>yes</option>
                                                                                <option>no</option>", 4)?>
                        <?php echo $html->getStaticSelectionBox("Output Directory", "outdir", "TEXT", 8)?>
                        <?php echo $html->getStaticSelectionBox("FastQC", "fastqc", "<option>yes</option>
                                                                            <option>no</option>", 4)?>
			
			<?php echo $html->startExpandingSelectionBox(6)?>
                        <?php echo $html->getExpandingSelectionBox("Barcode Separation", "barcodes", 2, 12, ["distance","format"], [[1,2,3,4,5],[
                                                                    "5 end read 1","3 end read 2 (or 3 end on single end)","barcode is in header (illumina casava)",
                                                                    "no barcode on read 1 of a pair (read 2 must have on 5 end)",
                                                                    "paired end both reads 5 end"]])?>
                        <?php echo $html->getExpandingSelectionBox("Adapter Removal", "adapter", 1, 12, ["adapter"], [["TEXTBOX"]])?>
                        
			<?php echo $html->getExpandingSelectionbOX("Custom Sequence Set", "custom", 1, 12, ["Add new Custom Sequence Set"], [["BUTTON"]])?>
			<?php echo $html->getExpandingSelectionBox("Additional Pipelines", "pipeline", 1, 12, ["Add a Pipeline"], [["BUTTON"]])?>
			<?php echo $html->endExpandingSelectionBox()?>
			
			<?php echo $html->startExpandingSelectionBox(6)?>
                        <?php echo $html->getExpandingCommonRNABox("Common RNAs", "commonind", 7, 12, ["ercc","rrna","mirna","trna","snrna","rmsk","genome"],
                                                                   [["no","yes"],["no","yes"],["no","yes"],["no","yes"],["no","yes"],["no","yes"],["no","yes"]])?>
                        <?php echo $html->getExpandingSelectionBox("Split FastQ", "split", 1, 12, ["number of reads per file"], [["TEXT","5000000"]])?>
                        <?php echo $html->getExpandingSelectionBox("Quality Filtering", "quality", 5, 12, ["window size","required quality","leading","trailing","minlen"],
                                                                   [["TEXT","10"],["TEXT","15"],["TEXT","5"],["TEXT","5"],["TEXT","36"]])?>
                        <?php echo $html->getExpandingSelectionBox("Trimming", "trim", 3, 12, ["single or paired-end", "5 length 1", "3 length 1"],
                                                                   [["ONCHANGE", "single-end", "paired-end"],["TEXT","0"],["TEXT","0"]])?>
			<?php echo $html->endExpandingSelectionBox()?>
			
			<div class="col-md-12">
			    <input type="button" id="submitPipeline" class="btn btn-primary" name="pipeline_send_button" value="Submit Pipeline" onClick="submitPipeline('selected');"/>
                        </div>
                    </div><!-- /.row -->
                </section><!-- /.content -->
                