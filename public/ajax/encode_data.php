<?php
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('report_errors','on');

require_once("../../config/config.php");
require_once("../../includes/dbfuncs.php");
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$query = new dbfuncs();

if (isset($_GET['p'])){$p = $_GET['p'];}
$data = '';

if($p == 'getSampleDataInfo')
{
	if (isset($_GET['samples'])){$samples = $_GET['samples'];}
	$data=$query->queryTable("SELECT ngs_samples.id, ngs_samples.name, ngs_samples.samplename, ngs_samples.title, concentration,
							 read_length, biological_replica, technical_replica, spike_ins, read_length,
							 molecule, genotype, treatment_manufacturer, instrument_model, adapter,
							 time, ngs_donor.id as did, donor, donor_acc, donor_uuid, biosample_type, series_id,
							 protocol_id, lane_id, organism, source, ngs_source.id as sid,
							 biosample_acc, biosample_uuid, library_acc, library_uuid, replicate_acc, replicate_uuid,
							 treatment_acc, treatment_uuid, experiment_acc, experiment_uuid
							 FROM ngs_samples
							 LEFT JOIN ngs_donor
							 ON ngs_donor.id = ngs_samples.donor_id
							 LEFT JOIN ngs_biosample_type
							 ON ngs_biosample_type.id = ngs_samples.biosample_type_id
							 LEFT JOIN ngs_organism
							 ON ngs_organism.id = ngs_samples.organism_id
							 LEFT JOIN ngs_molecule
							 ON ngs_molecule.id = ngs_samples.molecule_id
							 LEFT JOIN ngs_treatment_manufacturer
							 ON ngs_treatment_manufacturer.id = ngs_samples.treatment_manufacturer_id
							 LEFT JOIN ngs_instrument_model
							 ON ngs_instrument_model.id = ngs_samples.instrument_model_id
							 LEFT JOIN ngs_genotype
							 ON ngs_genotype.id = ngs_samples.genotype_id
							 LEFT JOIN ngs_source
							 ON ngs_source.id = ngs_samples.source_id
							 WHERE ngs_samples.id IN ( $samples )");
}
else if($p == "getLaneDataInfo")
{
	if (isset($_GET['lanes'])){$lanes = $_GET['lanes'];}
	$data=$query->queryTable("SELECT ngs_lanes.id, ngs_lanes.name, sequencing_id, ngs_lanes.lane_id,
							 facility, cost, date_submitted, date_received, total_reads,
							 phix_requested, phix_in_lane, total_samples, resequenced
							 FROM ngs_lanes
							 LEFT JOIN ngs_facility
							 ON ngs_facility.id = ngs_lanes.facility_id
							 WHERE ngs_lanes.id IN ( $lanes )");
}
else if($p == 'getSeriesDataInfo')
{
	if (isset($_GET['series'])){$series = $_GET['series'];}
	$data=$query->queryTable("SELECT ngs_experiment_series.id, experiment_name, summary, design, organization, lab, ngs_experiment_series.grant
							 FROM ngs_experiment_series
							 LEFT JOIN ngs_organization
							 ON ngs_organization.id = ngs_experiment_series.organization_id
							 LEFT JOIN ngs_lab
							 ON ngs_lab.id = ngs_experiment_series.lab_id
							 WHERE ngs_experiment_series.id = $series");
}
else if ($p == 'getProtocolDataInfo')
{
	if (isset($_GET['protocols'])){$protocols = $_GET['protocols'];}
	$data=$query->queryTable("SELECT ngs_protocols.id, name, growth, treatment,
							 extraction, library_construction, crosslinking_method, fragmentation_method,
							 strand_specific, library_strategy
							 FROM ngs_protocols
							 LEFT JOIN ngs_library_strategy
							 ON ngs_protocols.library_strategy_id = ngs_library_strategy.id
							 WHERE ngs_protocols.id in ( $protocols )");
}
else if ($p == 'getAntibodyDataInfo')
{
	$data=$query->queryTable("SELECT * FROM ngs_antibody_target");
}
else if ($p == 'submitAccessionAndUuid')
{
	if (isset($_GET['item'])){$item = $_GET['item'];}
	if (isset($_GET['table'])){$table = $_GET['table'];}
	if (isset($_GET['type'])){$type = $_GET['type'];}
	if (isset($_GET['accession'])){$accession = $_GET['accession'];}
	if (isset($_GET['uuid'])){$uuid = $_GET['uuid'];}
	
	if($type == 'treatment' || $type == "replicate"){
		$data=$query->runSQL("UPDATE `" . $table . "` " .
							"SET `" . $type . "_uuid` = '" . $uuid . "' " .
							"WHERE id = " . $item);
	}else{
		$data=$query->runSQL("UPDATE `" . $table . "` " .
							"SET `" . $type . "_acc` = '" . $accession . "', `" . $type . "_uuid` = '" . $uuid . "' " .
							"WHERE id = " . $item);	
	}
}
else if ($p == 'gatherFileData')
{
	///	Gather filedata to post or patch into ENCODE	///
	
	//	Obtain samplename, sample id, and the directory id
	if (isset($_GET['sample'])){$sample = $_GET['sample'];};
	if (isset($_GET['sample_id'])){$sample_id = $_GET['sample_id'];};
	if (isset($_GET['dir_id'])){$dir_id = $_GET['dir_id'];};
	//	Select the out directory of the flagged run
	$fastq_info = $query->queryAVal("
	SELECT outdir
	FROM ngs_runparams
	WHERE id in (SELECT run_id FROM ngs_runlist WHERE sample_id = ".$sample_id.")
	AND run_flag = 1;
	");
	//	If the initial run, remove initial run from the path
	$fastq_info=str_replace("/initial_run","",$fastq_info);
	//	select the backup directory from the specified directory id
	$dir_info = $query->queryAVal("
	SELECT backup_dir
	FROM ngs_dirs
	WHERE id = $dir_id
	");
	//	FASTQ FIND
	//	Use find to search for given fastq files based on the flagged run's out directory
	$cmd1 = 'find '.$fastq_info.' -regex ".*\.\(fastq\|fastq.gz\|)\)" | grep "'.$sample.'" | grep -v "tmp" | grep -v "sorted" | grep -v "seqmapping" | grep -v "rsem" | grep -v "picard" | grep -v "initial_run" | grep -v "input"';
	$OPEN1 = popen( $cmd1, "r" );
    $FREAD1 = fread($OPEN1, 2096);
	$CLOSE1 = pclose($OPEN1);
	//	BAM FIND
	//	Use find to search for given bam files based on the directory id given
	$cmd2 = 'find '.$dir_info.' -regex ".*\.\(bam\|)\)" | grep "'.$sample.'" | grep -v "tmp" | grep -v "sorted" | grep -v "seqmapping" | grep -v "rsem" | grep -v "picard" | grep -v "initial_run"';
	$OPEN2 = popen( $cmd2, "r" );
    $FREAD2 = fread($OPEN2, 2096);
	$CLOSE2 = pclose($OPEN2);
	//	Combine files into one array, disregard empty strings
	$file_to_submit = array();
	foreach(explode("\n", $FREAD1) as $read){
		if($read != ""){
			array_push($file_to_submit, $read);
		}
	}
	foreach(explode("\n", $FREAD2) as $read){
		if($read != ""){
			array_push($file_to_submit, $read);
		}
	}
	//	Search for files that have already been submitted
	$submit_check=json_decode($query->queryTable("
	SELECT id, filename
	FROM ngs_filedata
	WHERE filename in ( '".implode("','",$file_to_submit)."' )
	"));
	//	Store file names that have already been submitted into an array for checking
	$no_submit_array = array();
	foreach($submit_check as $sc){
		if(in_array($sc->filename, $file_to_submit)){
			array_push($no_submit_array, $sc->filename);
		}
	}
	//	If filename is not in the database already, submit it into the database
	foreach($file_to_submit as $file ){
		if(!in_array($file, $no_submit_array)){
			$insert=$query->runSQL("
			INSERT INTO ngs_filedata
			( `sample_id`, `filename` )
			VALUES
			( ".$sample_id.", '".$file."' )
			");
		}
	}
	//	Return all files to be either posted or patched
	$data=json_encode($file_to_submit);
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo $data;
exit;
?>