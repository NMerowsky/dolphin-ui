<?php
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('report_errors','on');

require_once("../../config/config.php");
require_once("../../includes/dbfuncs.php");

$query = new dbfuncs();

$pDictionary = ['getSelectedSamples', 'submitPipeline', 'getStatus', 'getRunSamples', 'grabReload', 'getReportNames', 'lanesToSamples',
                'checkMatePaired', 'getAllSampleIds', 'getLaneIdFromSample', 'getSingleSample', 'getSeriesIdFromLane', 'getAllLaneIds'];

$q = "";
$r = "";
$seg = "";
$search = "";

if (isset($_GET['p'])){$p = $_GET['p'];}
if (isset($_GET['q'])){$q = $_GET['q'];}
if (isset($_GET['r'])){$r = $_GET['r'];}
if (isset($_GET['seg'])){$seg = $_GET['seg'];}
if (isset($_GET['search'])){$search = $_GET['search'];}

if (isset($_GET['start'])){$start = $_GET['start'];}
if (isset($_GET['end'])){$end = $_GET['end'];}

//make the q val proper for queries
if($q == "Assay"){ $q = "library_type"; }
else { $q = strtolower($q); }

if($search != "" && !in_array($p, $pDictionary)){
	//Prepare search query
	$searchQuery = "";
	$splt = explode("$", $search);
	foreach ($splt as $s){
		$queryArray = explode("=", $s);
		$spltTable = $queryArray[0];
		$spltValue = $queryArray[1];
		$searchQuery .= "biocore.ngs_samples.$spltTable = \"$spltValue\"";
		if($s != end($splt)){
			$searchQuery .= " AND ";
		}
	}
	//browse (search incnluded)
	if($seg == "browse")
	{
		if($p == "getLanes")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id,name, facility, total_reads, total_samples
			FROM biocore.ngs_lanes
			WHERE biocore.ngs_lanes.id
			IN (SELECT biocore.ngs_samples.lane_id FROM biocore.ngs_samples WHERE $searchQuery) $time
			");
		}
		else if($p == "getSamples")
		{
			$time="";
			if (isset($start)){$time="and `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, title, source, organism, molecule
			FROM biocore.ngs_samples
			WHERE $searchQuery $time
			");
		}
		else if($p == "getExperimentSeries")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, experiment_name, summary, design
			FROM biocore.ngs_experiment_series
			WHERE biocore.ngs_experiment_series.id
			IN (SELECT biocore.ngs_samples.series_id FROM biocore.ngs_samples WHERE $searchQuery) $time
			");
		}
	}
	else
	{
		//details (search included)
		if($p == "getLanes" && $q != "")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id,name, facility, total_reads, total_samples
			FROM biocore.ngs_lanes
			WHERE biocore.ngs_lanes.id
			IN (SELECT biocore.ngs_samples.lane_id FROM biocore.ngs_samples WHERE $searchQuery)
			AND biocore.ngs_lanes.series_id = $q $time
			");
		}
		else if($p == "getSamples" && $r != "")
		{
			$time="";
			if (isset($start)){$time="and `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, title, source, organism, molecule
			FROM biocore.ngs_samples
			WHERE $searchQuery
			AND biocore.ngs_samples.lane_id = $r $time
			");
		}
		else if($p == "getSamples" && $q != "")
		{
			$time="";
			if (isset($start)){$time="and `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, title, source, organism, molecule
			FROM biocore.ngs_samples
			WHERE $searchQuery
			AND biocore.ngs_samples.series_id = $q $time
			");
		}
		else if($p == "getExperimentSeries")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, experiment_name, summary, design
			FROM biocore.ngs_experiment_series
			WHERE biocore.ngs_experiment_series.id
			IN (SELECT biocore.ngs_samples.series_id FROM biocore.ngs_samples WHERE $searchQuery) $time
			");
		}
	}
}
else if (!in_array($p, $pDictionary))
{
	//browse (no search)
	if($seg == "browse")
	{
		if($p == "getExperimentSeries")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, experiment_name, summary, design
			FROM biocore.ngs_experiment_series
			WHERE biocore.ngs_experiment_series.id
			IN (SELECT biocore.ngs_samples.series_id FROM biocore.ngs_samples WHERE ngs_samples.$q = \"$r\") $time
			");
		}
		else if($p == "getLanes")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id,name, facility, total_reads, total_samples
			FROM biocore.ngs_lanes
			WHERE biocore.ngs_lanes.id
			IN (SELECT biocore.ngs_samples.lane_id FROM biocore.ngs_samples WHERE biocore.ngs_samples.$q = \"$r\") $time
			");
		}
		else if($p == "getSamples")
		{
			$time="";
			if (isset($start)){$time="and `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, title, source, organism, molecule
			FROM biocore.ngs_samples
			WHERE biocore.ngs_samples.$q = \"$r\" $time
			");
		}
		else if($p == "getProtocols")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, name, growth, treatment
			FROM biocore.ngs_protocols
			WHERE biocore.ngs_samples.$q = \"$r\" $time
			");
		}
	}
	else
	{
		//details (no search)
		if($p == "getLanes" && $q != "")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id,name, facility, total_reads, total_samples
			FROM biocore.ngs_lanes
			WHERE biocore.ngs_lanes.series_id = $q $time
			");
		}
		else if($p == "getSamples" && $r != "")
		{
			$time="";
			if (isset($start)){$time="and `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, title, source, organism, molecule
			FROM biocore.ngs_samples
			WHERE biocore.ngs_samples.lane_id = $r $time
			");
		}
		else if($p == "getSamples" && $q != "")
		{
			$time="";
			if (isset($start)){$time="and `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, title, source, organism, molecule
			FROM biocore.ngs_samples
			WHERE biocore.ngs_samples.series_id = $q $time
			");
		}
		//index
		else if($p == "getExperimentSeries")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, experiment_name, summary, design
			FROM biocore.ngs_experiment_series $time
			");
		}
		else if($p == "getProtocols")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, name, growth, treatment
			FROM biocore.ngs_protocols $time
			");
		}

		else if($p == "getLanes")
		{
			$time="";
			if (isset($start)){$time="WHERE `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id,name, facility, total_reads, total_samples
			FROM biocore.ngs_lanes $time
			");
		}
		else if($p == "getSamples")
		{
			$time="";
			if (isset($start)){$time="and `date_created`>='$start' and `date_created`<='$end'";}
			$data=$query->queryTable("
			SELECT id, title, source, organism, molecule
			FROM biocore.ngs_samples $time
			");
		}
	}
}
else if ($p == "getSelectedSamples")
{

	//Prepare selected sample search query
	$searchQuery = "";
	$splitIndex = ['id','lane_id'];
	$typeCount = 0;
	if (substr($search, 0, 1) == "$"){
		//only lanes selected
		$search = substr($search, 1, strlen($search));
		$splt = explode(",", $search);
		foreach ($splt as $x){
			$searchQuery .= "biocore.ngs_samples.$splitIndex[1] = $x";
			if($x != end($splt)){
				$searchQuery .= " OR ";
			}
		}
	}
	else if(substr($search, strlen($search) - 1, strlen($search)) == "$"){
		//only samples selected
		$search = substr($search, 0, strlen($search) - 1);
		$splt = explode(",", $search);
		foreach ($splt as $x){
			$searchQuery .= "biocore.ngs_samples.$splitIndex[0] = $x";
			if($x != end($splt)){
				$searchQuery .= " OR ";
			}
		}
	}
	else{
		$splt = explode("$", $search);
		foreach ($splt as $s){
			$secondSplt = explode(",", $s);
			foreach ($secondSplt as $x){
				$searchQuery .= "biocore.ngs_samples.$splitIndex[$typeCount] = $x";
				if($x != end($secondSplt)){
					$searchQuery .= " OR ";
				}
			}
			if($s != end($splt)){
					$searchQuery .= " OR ";
			}
			$typeCount = $typeCount + 1;
		}
	}
	$time="";
	if (isset($start)){$time="and `date_created`>='$start' and `date_created`<='$end'";}
	$data=$query->queryTable("
	SELECT id, title, source, organism, molecule
	FROM biocore.ngs_samples
	WHERE $searchQuery $time
	");
}
else if ($p =='getStatus')
{
	$time="";
	if (isset($start)){$time="and `date_created`>='$start' and `date_created`<='$end'";}
	$data=$query->queryTable("
	SELECT id, run_group_id, run_name, outdir, run_description, run_status
	FROM biocore.ngs_runparams
	$time
	");
}
else if($p == 'getRunSamples')
{
	//Grab Variables
	if (isset($_GET['runID'])){$runID = $_GET['runID'];}

	$data=$query->queryTable("
	SELECT sample_id
	FROM biocore.ngs_runlist
	WHERE biocore.ngs_runlist.run_id = $runID
	");
}
else if ($p == 'grabReload')
{
	//Grab variables
	if (isset($_GET['groupID'])){$groupID = $_GET['groupID'];}

	$data=$query->queryTable("
	SELECT outdir, json_parameters, run_name, run_description
	FROM biocore.ngs_runparams
	WHERE biocore.ngs_runparams.id = $groupID
	");
}
else if ($p == 'getReportNames')
{
	if (isset($_GET['samp'])){$samp = $_GET['samp'];}
	if (isset($_GET['runid'])){$runid = $_GET['runid'];}

	$sampleQuery = '';
	foreach($samp as $s){
		$sampleQuery.= 'ngs_runlist.sample_id = '+ $s;
		if($s != end($samp)){
			$sampleQuery.= ' OR ';
		}
	}

	$data=$query->queryTable("
		SELECT distinct(ngs_fastq_files.file_name), ngs_runparams.outdir
		FROM ngs_fastq_files, ngs_runparams, ngs_runlist
		WHERE ngs_runlist.sample_id = ngs_fastq_files.sample_id
		AND ngs_runparams.id = ngs_fastq_files.lane_id
			AND ngs_fastq_files.lane_id = $runid
			AND ( $sampleQuery );
	");
}
else if ($p == 'lanesToSamples')
{
	if (isset($_GET['lane'])){$lane = $_GET['lane'];}
	$data=$query->queryTable("
		SELECT id
		FROM ngs_samples
		WHERE ngs_samples.lane_id = $lane
	");
}
else if ($p == 'getAllSampleIds')
{
	$data=$query->queryTable("
		SELECT id
		FROM ngs_samples
	");
}
else if ($p == 'getAllLaneIds')
{
	$data=$query->queryTable("
		SELECT id
		FROM ngs_lanes
	");
}
else if ($p == 'getLaneIdFromSample')
{
	if (isset($_GET['sample'])){$sample = $_GET['sample'];}
	$data=$query->queryTable("
		SELECT id
		FROM ngs_lanes
		where id =
				(select lane_id
				from ngs_samples
				where ngs_samples.id = $sample);
	");
}
else if($p == 'getSingleSample')
{
	if (isset($_GET['sample'])){$sample = $_GET['sample'];}
	$data=$query->queryTable("
		SELECT id, title
		FROM ngs_samples
		where id = $sample
	");
}
else if($p == 'getSeriesIdFromLane')
{
	if (isset($_GET['lane'])){$lane = $_GET['lane'];}
	$data=$query->queryTable("
		SELECT series_id
		FROM ngs_lanes
		where id = $lane
	");
}
else if ($p == 'checkMatePaired')
{
    if (isset($_GET['runid'])){$runid = $_GET['runid'];}
	$data=$query->queryTable("
		SELECT json_parameters
		FROM ngs_runparams
		where id = $runid
	"); 
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo $data;
exit;
?>
