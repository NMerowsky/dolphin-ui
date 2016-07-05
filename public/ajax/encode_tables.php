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

if ($p == 'obtainDonors'){
	$data=$query->queryTable("
		SELECT *
		FROM ngs_donor
		WHERE donor_acc IS NOT NULL
		OR donor_uuid IS NOT NULL");
}else if ($p == 'obtainSamples'){
	$data=$query->queryTable("
		SELECT *
		FROM ngs_samples
		WHERE experiment_acc IS NOT NULL
		OR experiment_uuid IS NOT NULL
		OR library_acc IS NOT NULL
		OR library_uuid IS NOT NULL
		OR biosample_acc IS NOT NULL
		OR biosample_uuid IS NOT NULL
		OR replicate_uuid IS NOT NULL");
}else if ($p == 'obtainTreatments'){
	$data=$query->queryTable("
		SELECT *
		FROM ngs_treatment
		WHERE uuid IS NOT NULL");
}else if ($p == 'obtainAntibodies'){
	$data=$query->queryTable("
		SELECT *
		FROM ngs_antibody_target
		WHERE antibody_lod_acc IS NOT NULL
		OR antibody_lot_uuid IS NOT NULL");
}else if ($p == 'obtainFiles'){
	$data=$query->queryTable("
		SELECT *
		FROM ngs_file_submissions
		WHERE file_acc IS NOT NULL
		OR file_uuid IS NOT NULL");
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo $data;
exit;
?>