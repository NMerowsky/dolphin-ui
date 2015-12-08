<?php
require_once("../../config/config.php");
require_once("../../includes/dbfuncs.php");

//Metadata submission
include('../php/Requests/library/Requests.php');
Requests::register_autoloader();
$query = new dbfuncs();

if (isset($_GET['sample_id'])){$sample_id = $_GET['sample_id'];}
if (isset($_GET['type'])){$type = $_GET['type'];}
if (isset($_GET['experiment'])){$experiment = $_GET['experiment'];}
if (isset($_GET['replicate'])){$replicate = $_GET['replicate'];}

$file_accs = [];
$file_uuids = [];

//Obtain database variables
$experiment_info = json_decode($query->queryTable("
	SELECT `lab`, `grant`
	FROM ngs_experiment_series
	LEFT JOIN ngs_lab
	ON ngs_lab.id = ngs_experiment_series.lab_id
	WHERE ngs_experiment_series.id =
	(SELECT series_id FROM ngs_samples WHERE id = $sample_id)"));
$file_query = json_decode($query->queryTable("
	SELECT ngs_file_submissions.id, ngs_file_submissions.dir_id, ngs_file_submissions.run_id,
	ngs_file_submissions.sample_id, ngs_file_submissions.file_name, ngs_file_submissions.file_type,
	ngs_file_submissions.file_md5, ngs_file_submissions.file_uuid, ngs_file_submissions.file_acc, outdir
	FROM ngs_file_submissions
	LEFT JOIN ngs_runparams
	ON ngs_runparams.id = ngs_file_submissions.run_id
	WHERE sample_id = " . $sample_id ));
$dir_query=json_decode($query->queryTable("
	SELECT fastq_dir, backup_dir, amazon_bucket
	FROM ngs_dirs
	WHERE id = " . $file_query[0]->dir_id));
//Needed?
$host = 'http://localhost:6543';

//Encoded access information
$encoded_access_key = 'SU45FB2Q';
$encoded_secret_access_key = 'rae76sr5bntlz5c6';

//Experiment Accession number
$dataset_acc = $experiment;

//Lab info
$my_lab = $experiment_info[0]->lab;
$my_award = $experiment_info[0]->grant;

//Other information
$encValData = 'encValData';
$assembly = 'hg19';
$replicate = "/replicates/" . $replicate . "/";

//For each file (single or paired end)
foreach($file_query as $fq){
	//File names (single or paired end)
	$file_names = explode(",",$fq->file_name);
	foreach($file_names as $fn){
		//File path
		if($fq->file_type = 'fastq' && strpos($fn, "/") == false){
			$directory = $dir_query[0]->backup_dir;
			if(substr($directory, -1) != '/'){
				$directory = $directory . "/";
			}
		}else{
			$directory = $fq->outdir;
			if(substr($directory, -1) != '/'){
				$directory = $directory . "/";
			}
		}
		$file_size = filesize($directory . $fn);
		//File checksum
		if(end($file_names) == $fn){
			$md5sum = end(explode(",",$fq->file_md5));
		}else{
			$md5sum = explode(",",$fq->file_md5)[0];
		}
		$data = array(
			"dataset" => $dataset_acc,
			"replicate" => $replicate,
			"file_size" => $file_size,
			"md5sum" => $md5sum,
			"output_type" => "reads",
			"read_length" => 101,
			"platform" => "ENCODE:HiSeq2000",
			"submitted_file_name" => end(explode("/",$fn)),
			"lab" => $my_lab,
			"award" => $my_award,
		);
		if($fq->file_type = 'fastq'){
			if(strpos($fn, "/") > -1){
				$step = "step5";
			}else{
				$step = "step1";
			}
			if(count($file_names) == 2){
				//	FASTQ PAIRED
				$data["file_format"] = 'fastq';
				$data["run_type"] = "paired-ended";
				if(end($file_names) == $fn){
					$data["aliases"] = array($my_lab.':'.$step.'_p2_'.end(explode("/",$fn)));
					$data["paired_end"] = '2';
					$data["paired_with"] = $my_lab.':'.$step.'_p1_'.$file_names[0];
				}else{
					$data["aliases"] = array($my_lab.':'.$step.'_p1_'.end(explode("/",$fn)));
					$data["paired_end"] = '1';
				}
			}else if (count($file_names) == 1){
				//	FASTQ SINGLE
				$data["file_format"] = 'fastq';
				$data["run_type"] = "single-ended";
				$data["aliases"] = array($my_lab.':'.$step.'_'.end(explode("/",$fn)));
			}
		}else if($fq->file_type = 'bam'){
			//	BAM
			$data["file_format"] = 'bam';
			$data['assembly'] = "hg19";
			$data["aliases"] = array($my_lab.'":"step7_'.end(explode("/",$fn)));
		}else if($fq->file_type = 'bigwig'){
			//	BIGWIG
			$data["file_format"] = 'bigWig';
			$data['assembly'] = "hg19";
			$data["aliases"] = array($my_lab.'":"step8_'.end(explode("/",$fn)));
		}else if($fq->file_type = 'tsv'){
			//	TSV
			$data["file_format"] = 'tsv';
			$data['assembly'] = "hg19";
			$data["aliases"] = array($my_lab.':step9_'.end(explode("/",$fn)));
		}
		$gzip_types = array(
			"CEL",
			"bam",
			"bed",
			"csfasta",
			"csqual",
			"fasta",
			"fastq",
			"gff",
			"gtf",
			"tar",
			"sam",
			"wig"
		);
		if(in_array($data['file_format'], $gzip_types) && explode('.',$file_name)[count(explode('.',$file_name))] == '.gz'){
			$is_gzipped = 'Expected gzipped file';
		}else{
			$is_gzipped = 'Expected un-gzipped file';
		}
		
		$chromInfo = '-chromInfo='.$encValData.'/'.$assembly.'/chrom.sizes';
		$validate_map = array(
			'fasta' =>  array(null => array('-type=fasta')),
			'fastq' =>  array(null => array('-type=fastq')),
			'bam' => array(null => array('-type=bam', $chromInfo)),
			'bigWig' => array(null => array('-type=bigWig', $chromInfo)),
			'bed' => array('bed3' => array('-type=bed3', $chromInfo),
								'bed6' => array('-type=bed6+', $chromInfo),
								'bedLogR' => array('-type=bed9+1', $chromInfo, '-as='.$encValData.'/as/bedLogR.as'),
								'bedMethyl' => array('-type=bed9+2', $chromInfo, '-as='.$encValData.'/as/bedMethyl.as'),
								'broadPeak' => array('-type=bed6+3', $chromInfo, '-as='.$encValData.'/as/broadPeak.as'),
								'gappedPeak' => array('-type=bed12+3', $chromInfo, '-as='.$encValData.'/as/gappedPeak.as'),
								'narrowPeak' => array('-type=bed6+4', $chromInfo, '-as='.$encValData.'/as/narrowPeak.as'),
								'bedRnaElements' => array('-type=bed6+3', $chromInfo, '-as='.$encValData.'/as/bedRnaElements.as'),
								'bedExonScore' => array('-type=bed6+3', $chromInfo, '-as='.$encValData.'/as/bedExonScore.as'),
								'bedRrbs' => array('-type=bed9+2', $chromInfo, '-as='.$encValData.'/as/bedRrbs.as'),
								'enhancerAssay' => array('-type=bed9+1', $chromInfo, '-as='.$encValData.'/as/enhancerAssay.as'),
								'modPepMap' => array('-type=bed9+7', $chromInfo, '-as='.$encValData.'/as/modPepMap.as'),
								'pepMap' => array('-type=bed9+7', $chromInfo, '-as='.$encValData.'/as/pepMap.as'),
								'openChromCombinedPeaks' => array('-type=bed9+12', $chromInfo, '-as'.$encValData.'s/as/openChromCombinedPeaks.as'),
								'peptideMapping' => array('-type=bed6+4', $chromInfo, '-as='.$encValData.'/as/peptideMapping.as'),
								'shortFrags' => array('-type=bed6+21', $chromInfo, '-as='.$encValData.'/as/shortFrags.as')
								),
			'bigBed' => array('bed3' => array('-type=bed3', $chromInfo),
									'bed6' => array('-type=bigBed6+', $chromInfo),
									'bedLogR' => array('-type=bigBed9+1', $chromInfo, '-as='.$encValData.'/as/bedLogR.as'),
									'bedMethyl' => array('-type=bigBed9+2', $chromInfo, '-as='.$encValData.'/as/bedMethyl.as'),
									'broadPeak' => array('-type=bigBed6+3', $chromInfo, '-as='.$encValData.'/as/broadPeak.as'),
									'gappedPeak' => array('-type=bigBed12+3', $chromInfo, '-as='.$encValData.'/as/gappedPeak.as'),
									'narrowPeak' => array('-type=bigBed6+4', $chromInfo, '-as='.$encValData.'/as/narrowPeak.as'),
									'bedRnaElements' => array('-type=bed6+3', $chromInfo, '-as='.$encValData.'/as/bedRnaElements.as'),
									'bedExonScore' => array('-type=bigBed6+3', $chromInfo, '-as='.$encValData.'/as/bedExonScore.as'),
									'bedRrbs' => array('-type=bigBed9+2', $chromInfo, '-as='.$encValData.'/as/bedRrbs.as'),
									'enhancerAssay' => array('-type=bigBed9+1', $chromInfo, '-as='.$encValData.'/as/enhancerAssay.as'),
									'modPepMap' => array('-type=bigBed9+7', $chromInfo, '-as='.$encValData.'/as/modPepMap.as'),
									'pepMap' => array('-type=bigBed9+7', $chromInfo, '-as='.$encValData.'/as/pepMap.as'),
									'openChromCombinedPeaks' => array('-type=bigBed9+12', $chromInfo, '-as='.$encValData.'/as/openChromCombinedPeaks.as'),
									'peptideMapping' => array('-type=bigBed6+4', $chromInfo, '-as='.$encValData.'/as/peptideMapping.as'),
									'shortFrags' => array('-type=bigBed6+21', $chromInfo, '-as='.$encValData.'/as/shortFrags.as')
									),
			'rcc' => array(null => array('-type=rcc')),
			'idat' => array(null => array('-type=idat')),
			'bedpe' => array(null => array('-type=bed3+', $chromInfo)),
			'bedpe' => array('mango' => array('-type=bed3+', $chromInfo)),
			'gtf' => array(null => array(null)),
			'tar' => array(null => array(null)),
			'tsv' => array(null => array(null)),
			'csv' => array(null => array(null)),
			'2bit' => array(null => array(null)),
			'csfasta' => array(null => array('-type=csfasta')),
			'csqual' => array(null => array('-type=csqual')),
			'CEL' => array(null => array(null)),
			'sam' => array(null => array(null)),
			'wig' => array(null => array(null)),
			'hdf5' => array(null => array(null)),
			'gff' => array(null => array(null))
		);
		
		$validate_args = $validate_map[$data['file_format']][null];
		echo json_encode($validate_args);
		
		$cmd = "../php/encodeValidate/validateFiles $validate_args " . $directory . $fn; 
		$VALIDATE = popen( $cmd, "r" );
		$VALIDATE_READ =fread($VALIDATE, 2096);
		echo $VALIDATE_READ;
		pclose($VALIDATE);
		
		
		$headers = array('Content-Type' => 'application/json', 'Accept' => 'application/json');
		
		$server_start = "https://ggr-test.demo.encodedcc.org/";
		//$server_start = "https://test.encodedcc.org/";
		$server_end = "/";	
		
		$auth = array('auth' => array($encoded_access_key, $encoded_secret_access_key));
		if($fq->file_acc == NULL){
			$url = $server_start . 'file' . $server_end;
			$response = Requests::post($url, $headers, json_encode($data), $auth);
			$body = json_decode($response->body);
			$inserted = true;
			array_push($file_accs, $body->{'@graph'}[0]->{'accession'});
			array_push($file_uuids, $body->{'@graph'}[0]->{'uuid'});
		}else{
			if(end($file_names) == $fn){
				$url = $server_start . 'file/' . end(explode(",",$fq->file_acc)) . $server_end;
			}else{
				$url = $server_start . 'file/' . explode(",",$fq->file_acc)[0] . $server_end;
			}
			$response = Requests::patch($url, $headers, json_encode($data), $auth);
			$body = json_decode($response->body);
		}
		
		$item = $body->{'@graph'}[0];
		
		if(end($file_names) == $fn){
			//echo $response->body;
		}else{
			//echo $response->body . ",";	
		}
		
		####################
		# POST file to S3
		
		$creds = $item->{'upload_credentials'};
		
		$envUpdate = 'AWS_ACCESS_KEY_ID=' . $creds->{'access_key'} . ' ;' .
			'AWS_SECRET_ACCESS_KEY=' . $creds->{'secret_key'} . ' ;' .
			'AWS_SECURITY_TOKEN=' . $creds->{'session_token'} . ' ;';
		
		$AWS_COMMAND_KEY = popen( $envUpdate, "r" );
		pclose($AWS_COMMAND_KEY);
		
		$cmd_aws_launch = "aws s3 cp ".$directory.$fn ." ".$creds->{'upload_url'};
		$AWS_COMMAND_DO = popen( $cmd, "r" );
		pclose($AWS_COMMAND_DO);
	}
	
	if(isset($inserted)){
		$file_update = json_decode($query->runSQL("
		UPDATE ngs_file_submissions
		SET `file_acc` = '" . implode(",",$file_accs) . "',
		`file_uuid` = '" . implode(",",$file_uuids) . "' 
		WHERE id = " . $fq->id));
	}
}

?>