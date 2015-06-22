<?php
class Ngsimport extends VanillaModel {
	public $series_id;
	public $sid;
	public $gid;
	public $uid;
	public $username;
	private $worksheet;
	private $sheetData;
	public $fastq_dir;
	public $backup_dir;
	public $amazon_bucket;
	public $samples=[];
	
	public $organismCheck;
	public $pairedEndCheck;
	public $laneArrayCheck;
	public $barcode;
	public $namesList;
	public $laneList;
	public $initialSubmission = [];
	
	//	Variable Classes
	//	METADATA
	public $experiment_name;
	public $summary;
	public $design;
	public $conts = [];
	
	//	LANES
	public $lane_arr = [];
	
	//	PROTOCOLS
	public $prot_arr = [];
	
	//	SAMPLES
	public $sample_arr = [];
	public $char_arr=[];
	
	//	FILES
	public $file_arr = [];
	
	//	Sheet Check bools
	public $final_check;
	
	function num2alpha($n){
		for($r = ""; $n >= 0; $n = intval($n / 26) - 1){
			$r = chr($n%26 + 0x41) . $r;
		}
		return $r;
	}
	
	function columnNumber($col){
		$col = str_pad($col,2,'0',STR_PAD_LEFT);
		$i = ($col{0} == '0') ? 0 : (ord($col{0}) - 64) * 26;
		$i += ord($col{1}) - 64;
		return $i;
	}
	
	function getGroup($username) {
        $groups = json_decode($this->query("select g.id from user_group ug, users u, groups g where ug.u_id=u.id and ug.g_id=g.id and username='$username'"), true);
        $group_str='';
        foreach ($groups as $group):
            $group_str.=$group['id'].",";
        endforeach;
        return rtrim($group_str, ",");
    }
	
	function parseExcel($gid, $sid, $worksheet, $sheetData, $passed_final_check) {
		$this->worksheet=$worksheet;
		$this->sheetData=$sheetData;
		$this->sid=$sid;
		$this->gid=$gid;
		$this->final_check = $passed_final_check;
		
		$text = '<li>'.$this->worksheet['worksheetName'].'<br />';

		//$text.='Rows: '.$this->worksheet['totalRows'].' Columns: '.$this->worksheet['totalColumns'].'<br />';
		//$text.='Cell Range: A1:'.$this->worksheet['lastColumnLetter'].$this->worksheet['totalRows'];
		//$text.='</li>';

		$this->username=$_SESSION['user'];
		$sql="select id from biocore.users where `username`='$this->username'";
		$this->uid=$this->query($sql, 1);
		
		//	Check Data
		if ( $this->worksheet['worksheetName']=="METADATA"){
			$text.=$this->getMeta();
		}
		elseif ( $worksheet['worksheetName']=="LANES"){
			$text.=$this->getLanes();
		}
		elseif ( $worksheet['worksheetName']=="PROTOCOLS"){
			$text.=$this->getProtocols();
		}
		elseif ( $worksheet['worksheetName']=="SAMPLES"){
			$text.=$this->getSamples();
		}
		elseif ( $worksheet['worksheetName']=="FILES"){
			$text.=$this->getFiles();
		}
		
		//	Process Data
		$parseArray = [$this->final_check, $text];
		return $parseArray;
	}
	
	function finalizeExcel($worksheet, $sheetData){
		$this->worksheet=$worksheet;
		$this->sheetData=$sheetData;
		
		$text = "";
		
		$text='<li>'.$this->worksheet['worksheetName'].'<br />';
		if ( $this->worksheet['worksheetName']=="METADATA"){
			$text.=$this->processMeta();
		}elseif ( $worksheet['worksheetName']=="LANES"){
			$text.=$this->processLanes();
		}elseif ( $worksheet['worksheetName']=="PROTOCOLS"){
			$text.=$this->processProtocols();
		}elseif ( $worksheet['worksheetName']=="SAMPLES"){
			$text.=$this->processSamples();
		}elseif ( $worksheet['worksheetName']=="FILES"){
			$text.=$this->processFiles();
			$text.=$this->successText("<BR><BR>Excel import successful!<BR>");
		}
		if(!isset($this->initialSubmission[5])){
			array_push($this->initialSubmission, $this->laneList);
		}
		$text.="<script type='text/javascript'>";
		$text.="var initialSubmission = '" . implode(",", $this->initialSubmission) . "';";
		$text.="var initialNameList = '" . $this->namesList . "';";
		$text.="</script>";
		
		return $text;
	}
	
	function errorText($text){
		return "<font color='red'>" . $text . "</font><BR>";
	}
	
	function warningText($text){
		return "<font color='#B45F04'>" . $text . "</font><BR>";
	}
	
	function successText($text){
		return "<font color='green'>" . $text . "</font><BR>";
	}
	
	function checkAlphaNumWithAddChars($extraChars, $data){
		return preg_match('/^[a-zA-Z0-9' . $extraChars . ']+$/', $data);
	}
	
	function getMeta() {
		//var_dump($sheetData);
		$meta_check = true;
		$text = "";
		/*
		 *	Read data columns
		 */
		for ($i=1;$i<=$this->worksheet['totalRows'];$i++)
		{
			if($this->sheetData[$i]["A"]=="title"){$this->experiment_name=$this->esc($this->sheetData[$i]["B"]);}
			if($this->sheetData[$i]["A"]=="summary"){$this->summary=$this->esc($this->sheetData[$i]["B"]);}
			if($this->sheetData[$i]["A"]=="overall design"){$this->design=$this->esc($this->sheetData[$i]["B"]);}
			if($this->sheetData[$i]["A"]=="organization"){$this->organization=$this->esc($this->sheetData[$i]["B"]);}
			if($this->sheetData[$i]["A"]=="lab"){$this->lab=$this->esc($this->sheetData[$i]["B"]);}
			if($this->sheetData[$i]["A"]=="grant"){$this->grant=$this->esc($this->sheetData[$i]["B"]);}
			if($this->sheetData[$i]["A"]=="contributor"){array_push($this->conts, $this->esc($this->sheetData[$i]["B"]));}
			if($this->sheetData[$i]["A"]=="fastq directory"){$this->fastq_dir=$this->esc($this->sheetData[$i]["B"]);}
			if($this->sheetData[$i]["A"]=="backup directory"){$this->backup_dir=$this->esc($this->sheetData[$i]["B"]);}
			if($this->sheetData[$i]["A"]=="amazon bucket"){$this->amazon_bucket=$this->esc($this->sheetData[$i]["B"]);}
			
			if($this->sheetData[$i]["A"]=="title"){
				array_push($this->initialSubmission, $this->esc($this->sheetData[$i]["B"]));
			}
			if($this->sheetData[$i]["A"]=="backup directory"){
					array_push($this->initialSubmission, $this->esc($this->sheetData[$i]["B"]));
			}
		}
		
		/*
		 *	Check for proper data input
		 */
		//	Experiment Name
		if(isset($this->experiment_name)){
			if(!$this->checkAlphaNumWithAddChars(' ', $this->experiment_name)){
				$text.= $this->errorText("title does not contain proper characters, please use alpha-numeric characters and spaces");
				$this->final_check = false;
				$meta_check = false;
			}
		}else{
			$text.= $this->errorText("title is required for submission");
			$this->final_check = false;
			$meta_check = false;
		}
		
		//	Summary
		if(!isset($this->summary)){
			$text.= $this->errorText("summary is required for submission");
			$this->final_check = false;
			$meta_check = false;
		}
		
		//	Design
		if(!isset($this->design)){
			$text.= $this->errorText("overall design is required for submission");
			$this->final_check = false;
			$meta_check = false;
		}
		
		//	Organization
		
		if(!isset($this->organization)){
			$this->organization = NULL;
		}
		
		//	Lab
		if(!isset($this->lab)){
			$this->lab = NULL;
		}
		
		//	Grant
		if(!isset($this->grant)){
			$this->grant = NULL;
		}
		
		//	Contributors
		if($this->conts == []){
			$text.= $this->warningText("No contributors specified, please make sure to add them later if desired");
		}
		
		//	Fastq Directory
		if($this->fastq_dir != null){
			//	fastq directory check to be implemented later
		}else{
			$text.= $this->errorText("fastq directory is required for submission");
			$this->final_check = false;
			$meta_check = false;
		}
		
		//	Backup Directory
		if($this->backup_dir != null){
			//	backup directory check to be implemented
		}else{
			$text.= $this->errorText("backup directory is required for submission");
			$this->final_check = false;
			$meta_check = false;
		}
		
		//	Amazon Bucket
		if($this->amazon_bucket != null){
			//	amazon bucket check to be implemented
		}else{
			$text.= $this->warningText("amazon bucket not specified, please make sure to add it later if desired");
		}
		
		if($meta_check){
			$text.= $this->successText('Formatting passed inspection!<BR>');
		}
		return $text;
	}
	
	function processMeta(){
		$text = "";
		$new_series = new series($this, $this->experiment_name,$this->summary,$this->design,
								$this->organization, $this->lab, $this->grant);
		$this->series_id=$new_series->getId();
		$text.="SERIES:".$new_series->getStat()."<BR>";
		$new_conts = new contributors($this, $this->conts);
		$text.= "CONT:".$new_conts->getStat()."<BR>";
		if (isset($this->fastq_dir)){
			$new_dirs = new dirs($this);
			$text.= "DIRS:".$new_dirs->getStat()."<BR>";
		}
		return $text;
	}

	function getLanes(){
		$lane_check = true;
		$lane_warning_check = false;
		$text = "";
		/*
		 *	For each row in the lanes worksheet
		 */
		for ($i=4;$i<=$this->worksheet['totalRows'];$i++)
		{
			/*
			 *	Read data columns
			 */
			$lane = new lane();
			for ($j='A';$j<=$this->worksheet['lastColumnLetter'];$j++)
			{
				if($this->sheetData[3][$j]=="Lane name"){$lane->name=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Sequencing id"){
					$lane->lane_id=$this->esc($this->sheetData[$i][$j]);
				}elseif($this->sheetData[3][$j]=="Lane id"){
					$lane->lane_id=$this->esc($this->sheetData[$i][$j]);
				}
				if($this->sheetData[3][$j]=="Sequencing facility"){$lane->facility=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Cost"){$lane->cost=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Date submitted"){$lane->date_submitted=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Date received"){$lane->date_received=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="% PhiX requested"){$lane->phix_requested=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="% PhiX in lane"){$lane->phix_in_lane=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="# of Samples"){$lane->total_samples=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Resequenced?"){$lane->resequenced=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Notes"){$lane->notes=$this->esc($this->sheetData[$i][$j]);}
			}
			
			/*
			 *	Check for proper data input
			 */
			//	Lane Name
			if(isset($lane->name)){
				if($this->checkAlphaNumWithAddChars('_-', $lane->name)){
					$this->lane_arr[$lane->name]=$lane;
				}else{
					$text.= $this->errorText("Lane name does not contain proper characters, please use alpha-numeric characters and underscores (row " . $i . ")");
					$this->final_check = false;
					$lane_check = false;
				}
			}else{
				$text.= $this->errorText("Lane name is required for submission (row " . $i . ")");
				$this->final_check = false;
				$lane_check = false;
			}
			
			//	Lane id
			if(isset($lane->lane_id)){
				if(!$this->checkAlphaNumWithAddChars('_-', $lane->lane_id)){
					$text.= $this->errorText("Lane id does not contain proper characters, please use alpha-numeric characters and underscores (row " . $i . ")");
					$this->final_check = false;
					$lane_check = false;
				}
			}else{
				$text.= $this->warningText("Lane id not specified.  Specific lane id set to 0, please change (row " . $i . ")");
				$lane->lane_id=0;
			}
			
			//	Other Values
			if($lane->facility == null || $lane->cost == null ||
				$lane->date_submitted ==  null || $lane->date_received == null ||
				$lane->total_reads == null || $lane->phix_requested == null ||
				$lane->phix_in_lane == null || $lane->total_samples == null ||
				$lane->resequenced == null || $lane->notes == null){
				$lane_warning_check = true;
			}
		}
		if($lane_warning_check){
			$text.= $this->warningText("Some optional columns missing data, please make sure to add them later if desired");
		}
		if($lane_check){
			$text.= $this->successText('Formatting passed inspection!<BR>');
		}
		return $text;
	}
	
	function processLanes(){
		$text = "";
		//echo json_encode($this->lane_arr);
		$new_lanes = new lanes($this, $this->lane_arr);
		$text="LANE:".$new_lanes->getStat()."</br>";
		#$text.="LANE:".$new_lanes->getSQL();
		return $text;
	}

	function getProtocols(){
		$prot_check = true;
		$prot_warning_check = false;
		$text = "";
		/*
		 *	For each row in the protocols worksheet
		 */
		for ($i=4;$i<=$this->worksheet['totalRows'];$i++)
		{
			/*
			 *	Read data columns
			 */
			$prot = new prot();
			for ($j='A';$j<=$this->worksheet['lastColumnLetter'];$j++)
			{
				if($this->sheetData[3][$j]=="protocol name"){$prot->name= $this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="growth protocol"){$prot->growth= $this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="extract protocol"){$prot->extraction= $this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="library construction protocol"){$prot->library_construction= $this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="crosslinking method"){$prot->crosslinking_method=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="fragmentation method"){$prot->fragmentation_method=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="strand-specific"){$prot->strand_specific=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="library strategy"){$prot->library_strategy= $this->esc($this->sheetData[$i][$j]);}
			}
			
			/*
			 *	Check for proper data input
			 */
			//	Protocol Name
			if(isset($prot->name)){
				$this->prot_arr[$prot->name]=$prot;	
			}else{
				$text.= $this->errorText("protocol name is required for submission (row " . $i . ")");
				$this->final_check = false;
				$prot_check = false;
			}
			
			//	Crosslinking Method
			if(!isset($prot->crosslinking_method)){
				$prot->crosslinking_method = NULL;
			}
			
			//	Fragmentation Method
			if(!isset($prot->fragmentation_method)){
				$prot->fragmentation_method = NULL;
			}
			
			//	Strand Specific
			if(!isset($prot->strand_specific)){
				$prot->strand_specific = NULL;
			}
			
			//	Other Values
			if($prot->growth == null || $prot->extraction == null || $prot->library_construction == null ||
				$prot->library_strategy == null || !isset($prot->crosslinking_method) ||
				$prot->fragmentation_method == null || $prot->strand_specific == null){
				$prot_warning_check = true;
			}
		}
		if($prot_warning_check){
			$text.= $this->warningText("Some optional columns missing data, please make sure to add them later if desired");
		}
		if($prot_check){
			$text.= $this->successText('Formatting passed inspection!<BR>');
		}
		return $text;
	}
	
	function processProtocols(){
		$text = "";
		//echo json_encode($this->prot_arr);
		$new_protocol = new protocols($this, $this->prot_arr);
		$text.= "PROT:".$new_protocol->getStat();
		//var_dump($sheetData);
		return $text;
	}

	function getSamples(){
		$samp_check = true;
		$samp_warning_check = false;
		$text = "";
		/*
		 *	For each row in the samples worksheet
		 */
		for ($i=4;$i<=$this->worksheet['totalRows'];$i++)
		{
			/*
			 *	Read data columns
			 */
			$samp = new sample();
			$tag = new tag();
			for ($k=0;$k!=$this->columnNumber($this->worksheet['lastColumnLetter']);$k++)
			{
				$j = $this->num2alpha($k);
				if($this->sheetData[3][$j]=="Sample name"){$samp->name=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Lane name"){$samp->lane_name=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Protocol name"){$samp->protocol_name=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="barcode"){$samp->barcode=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="title"){$samp->title=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="batch id"){$samp->batch=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="source symbol"){$samp->source_symbol=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="source name"){$samp->source=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="organism"){$samp->organism=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="biosample type"){$samp->biosample_type=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="molecule"){$samp->molecule=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="description"){$samp->description=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="instrument model"){$samp->instrument_model=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="average insert size"){$samp->avg_insert_size=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="read length"){$samp->read_length=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Genotype"){$samp->genotype=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Condition Symbol"){$samp->condition_symbol=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Condition"){$samp->condition=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="concentration"){$samp->concentration=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="treatment manufacturer"){$samp->treatment_manufacturer=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Donor"){$samp->donor=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Time"){$samp->time=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Biological Replica"){$samp->biological_replica=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Technical Replica"){$samp->technical_replica=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="spikeIns"){$samp->spikeins=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="3' Adapter sequence"){$samp->adapter=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Notebook reference"){$samp->notebook_ref=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="Notes"){$samp->notes=$this->esc($this->sheetData[$i][$j]);}
	
				if($this->sheetData[3][$j]=="Sample name"){
					if($this->namesList == null){
						$this->namesList = $samp->name;
					}else{
						$this->namesList .= "," . $samp->name;
					}
				}
				if($this->sheetData[3][$j]=="Lane name"){
					if ($this->laneList == null){
						$this->laneList = $samp->lane_name;
					}
				}
				if($this->sheetData[3][$j]=="organism" && $this->organismCheck == null){
					array_push($this->initialSubmission, $this->esc($this->sheetData[$i][$j]));
					$this->organismCheck = 'check';
				}
	
				$valid = "/^characteristics:\s+?(.*)/";
	
				if(preg_match( $valid, $this->sheetData[3][$j], $matches) == 1)
				{
					if ($matches[1] != "tag" && $this->sheetData[$i][$j]!="")
					{
						$tag->tag=$matches[1];
						$tag->value=$this->sheetData[$i][$j];
						$tag->sample_name=$samp->name;
						$this->char_arr[$samp->name]=$tag;
					}
				}
			}
			
			/*
			 *	Check for proper data input
			 */
			//	Sample Name
			if(isset($samp->name)){
				if($this->checkAlphaNumWithAddChars('_-', $samp->name)){
					//	Need to check the database for similar names as well at a later date
					if(isset($this->sample_arr[$samp->name])){
						$text.= $this->errorText("Sample name already exists in that lane (row " . $i . ")");
						$this->final_check = false;
						$samp_check = false;
					}else{
						$this->sample_arr[$samp->name]=$samp;
					}
				}else{
					$text.= $this->errorText("Sample name does not contain proper characters, please use alpha-numeric characters and underscores (row " . $i . ")");
					$this->final_check = false;
					$samp_check = false;
				}
			}else{
				$text.= $this->errorText("Sample name is required for submission (row " . $i . ")");
				$this->final_check = false;
				$samp_check = false;
			}
			
			//	Lane Name
			//	For now, it's just checking the Lane given in the excel file, possible to check the database later
			if(isset($samp->lane_name)){
				if(!isset($this->lane_arr[$samp->lane_name])){
					$text.= $this->errorText("Lane name does not match any lane given in the excel file (row " . $i . ")");
					$this->final_check = false;
					$samp_check = false;
				}
			}else{
				$text.= $this->errorText("Lane name is required for submission (row " . $i . ")");
				$this->final_check = false;
				$samp_check = false;
			}
			
			//	Protocol Name
			if(!isset($this->prot_arr[$samp->protocol_name])){
				$text.= $this->errorText("Protocol name does not match any protocol given in the excel file (row " . $i . ")");
					$this->final_check = false;
					$samp_check = false;
			}
			
			//	Batch ID
			if(!isset($samp->batch)){
				$samp->batch = NULL;
			}
			
			//	Source Symbol
			if(!isset($samp->source_symbol)){
				$samp->source_symbol = NULL;
			}
			
			//	Condition Symbol
			if(!isset($samp->condition_symbol)){
				$samp->condition_symbol = NULL;
			}
			
			//	Concentration
			if(!isset($samp->concentration)){
				$samp->concentration = NULL;
			}
			
			//	Treatment Manufacturer
			if(!isset($samp->treatment_manufacturer)){
				$samp->treatment_manufacturer = NULL;
			}
			
			//	Biosample Type
			if(!isset($samp->biosample_type)){
				$samp->biosample_type = NULL;
			}
			
			//	Donor
			if(!isset($samp->donor)){
				$samp->donor = NULL;
			}
			
			//	Time
			if(!isset($samp->time)){
				$samp->time = NULL;
			}
			
			//	Biological Replica
			if(!isset($samp->biological_replica)){
				$samp->biological_replica = NULL;
			}
			
			//	Technical Replica
			if(!isset($samp->technical_replica)){
				$samp->technical_replica = NULL;
			}
			
			//	Spikeins
			if(!isset($samp->spikeins)){
				$samp->spikeins = NULL;
			}
			
			//	Other Values
			if(!isset($samp->title) ||
				$samp->source == null || $samp->organism == null || !isset($samp->condition_symbol) ||
				$samp->batch == null || $samp->source_symbol == null || $samp->biosample_type == null ||
				$samp->molecule == null || $samp->description == null || $samp->instrument_model == null ||
				$samp->avg_insert_size == null || $samp->read_length == null || $samp->genotype == null ||
				$samp->condition == null || $samp->adapter == null || $samp->notebook_ref == null ||
				$samp->notes == null || $samp->concentration == null || $samp->treatment_manufacturer == null ||
				$samp->donor == null || $samp->time == null || $samp->biological_replica == null ||
				$samp->technical_replica == null || $samp->spikeins == null){
				$samp_warning_check = true;
			}
		}
		if($samp_warning_check){
			$text.= $this->warningText("Some optional columns missing data, please make sure to add them later if desired");
		}
		if($samp_check){
			$text.= $this->successText('Formatting passed inspection!<BR>');
		}
		return $text;
	}
	
	function processSamples(){
		$text = "";
		//echo json_encode($this->sample_arr);
		$this->samples=$this->sample_arr;
		$new_samples = new samples($this, $this->sample_arr);
		$text="SAMPLE:".$new_samples->getStat()."<BR>";
		$new_chars = new characteristics($this, $this->char_arr);
		$text.="CHAR:".$new_chars->getStat();
		return $text;
	}
	
	function getFiles(){
		$file_check = true;
		$text = "";
		/*
		 *	For each row in the samples worksheet
		 */
		for ($i=4;$i<=$this->worksheet['totalRows'];$i++)
		{
			/*
			 *	Read data columns
			 */
			$file = new file();
			for ($j='A';$j<=$this->worksheet['lastColumnLetter'];$j++)
			{
				if($this->sheetData[3][$j]=="Sample or Lane Name (Enter same name for multiple files)"){$file->name=$this->esc($this->sheetData[$i][$j]);}
				if($this->sheetData[3][$j]=="file name(comma separated for paired ends)"){$file->file_name=$this->esc($this->sheetData[$i][$j]);$file->file_name=preg_replace('/\s/', '', $file->file_name);}
				if($this->sheetData[3][$j]=="file checksum"){$file->checksum=$this->esc($this->sheetData[$i][$j]);}
				
				if($this->sheetData[3][$j]=="Sample or Lane Name (Enter same name for multiple files)"){
					if(isset($this->lane_arr[$file->name]) && $this->laneArrayCheck == null){
						array_push($this->initialSubmission, 'lane');
						$this->laneArrayCheck = 'lane';
					}else if(isset($this->sample_arr[$file->name]) && $this->laneArrayCheck == null){
						array_push($this->initialSubmission, 'sample');
						$this->laneArrayCheck = 'sample';
					}
				}
				if($this->sheetData[3][$j]=="file name(comma separated for paired ends)" && $this->pairedEndCheck == null){
					if (strpos($file->file_name, ',') !== false){
						array_push($this->initialSubmission, 'paired');
						$this->pairedEndCheck = 'paired';
					}
				}
			}
			
			/*
			 *	Check for proper data input
			 */
			//	Sample/Lane Name
			if(isset($file->name)){
				if($this->checkAlphaNumWithAddChars('_-', $file->name)){
					if(!(isset($this->sample_arr[$file->name])) & !(isset($this->lane_arr[$file->name]))){
						$text.= $this->errorText("sample/lane name does not match the samples/lanes given (row " . $i . ")");
						$this->final_check = false;
						$file_check = false;
					}
				}else{
					$text.= $this->errorText("sample/lane name does not contain proper characters, please use alpha-numeric characters and underscores (row " . $i . ")");
					$this->final_check = false;
					$file_check = false;
				}
			}else{
				$text.= $this->errorText("sample/lane name is required for submission (row " . $i . ")");
				$this->final_check = false;
				$file_check = false;
			}
			
			//	File Name
			if(isset($file->file_name)){
				$this->file_arr[$file->file_name]=$file;
			}else{
				$text.= $this->errorText("file name is required for submission (row " . $i . ")");
				$this->final_check = false;
				$file_check = false;
			}
		}
		if($file_check){
			$text.= $this->successText('Formatting passed inspection!<BR>');
		}
		return $text;
	}
	
	function processFiles(){
		$text = "";
		//echo json_encode($this->file_arr);
		$new_files = new files($this, $this->file_arr, $this->samples);
		$text.="FILES:".$new_files->getStat();
		//var_dump($sheetData);
		return $text;
	}
}

/* main class */
class main{
	public $model;
	public $insert=0;
	public $update=0;
	public $sql;

	function process($my) {
		return ($my->getId()>0 ? $my->update(): $my->insert());
	}

	function processArr($arr) {
		foreach ($arr as $val) {
			$this->getId($val)>0 ? $this->update($val): $this->insert($val);
		}
	}
	function correct_date($date) {
		if ($date=='') {
	 return "null";
		}
		else
		{
	 return "STR_TO_DATE('$date', '%m-%d-%Y')";
		}
	}
	function correct_bool($field) {
		return $field=="yes" ? "true" : "false";
	}
}

/* Series Definitions */
class series extends main{
	private $experiment_name;
	private $summary;
	private $design;
	private $organization;
	private $lab;
	private $grant;

	function __construct($model, $experiment_name, $summary, $design, $organization, $lab, $grant)
	{
		$this->experiment_name=$experiment_name;
		$this->summary=$summary;
		$this->design=$design;
		$this->organization=$organization;
		$this->lab=$lab;
		$this->grant=$grant;
		$this->model = $model;
		$this->process($this);
	}
	function getStat()
	{
		return "Update:$this->update, Insert:$this->insert";
	}

	function getId()
	{
		$sql="select id from biocore.ngs_experiment_series where `experiment_name`='$this->experiment_name'";
		return $this->model->query($sql, 1);
	}
	function insert()
	{

		$sql="insert into biocore.ngs_experiment_series(`experiment_name`, `summary`, `design`,
			`organization`, `lab`, `grant`, `owner_id`, `group_id`, `perms`, `date_created`,
			`date_modified`, `last_modified_user`)
			values('$this->experiment_name', '$this->summary', '$this->design',
			'$this->organization', '$this->lab', '$this->grant',
			'".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
			now(), now(), '".$this->model->uid."');";

		$this->insert++;
		return $this->model->query($sql);
	}

	function update()
	{
		$sql="update biocore.ngs_experiment_series set
			`summary`='$this->summary',
			`design`='$this->design',
			`organization`='$this->organization',
			`lab`='$this->lab',
			`grant`='$this->grant',
			`group_id`='".$this->model->gid."',
			`perms`='".$this->model->sid."',
			`date_modified`=now(),
			`last_modified_user`='".$this->model->uid."' where `id` = ".$this->getId();
		$this->update++;
		//return $sql;
		return $this->model->query($sql);
	}
}

/* Contributors class */
class contributors extends main{
	private $conts=[];

	function __construct($model, $conts = [])
	{
		$this->conts = $conts;
		$this->model = $model;

		$this->processArr($conts);
	}

	function getStat()
	{
		return "Update:$this->update, Insert:$this->insert";
	}


	function getId($val)
	{
		$sql="select id from biocore.ngs_contributors where `contributor`='$val'";
		return $this->model->query($sql,1);
	}
	function insert($val)
	{
		$sql="insert into biocore.ngs_contributors(`series_id`, `contributor`, `owner_id`, `group_id`, `perms`, `date_created`, `date_modified`, `last_modified_user`)
			values('".$this->model->series_id."', '$val', '".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."', now(), now(), '".$this->model->uid."');";
		$this->insert++;
		return $this->model->query($sql);
	}

	function update($val)
	{
		$sql="update biocore.ngs_contributors set `contributor`='$val',
		`group_id`='".$this->model->gid."', `perms`='".$this->model->sid."',
		`date_modified`=now(), `last_modified_user`='".$this->model->uid."'where `id` =".$this->getId($val);
		$this->update++;

		return $this->model->query($sql);
	}

}


/* Lanes class */
class lane{}
class lanes extends main{
	private $lane_arr=[];

	function __construct($model, $lane_arr = [])
	{
		$this->lane_arr = $lane_arr;
		$this->model=$model;

		$this->processArr($lane_arr);
	}

	function getStat()
	{
		return "Update:$this->update, Insert:$this->insert";
	}

	function getId($lane)
	{
		$sql="select id from biocore.ngs_lanes where `name`='$lane->name' and `series_id`='".$this->model->series_id."'";
		return $this->model->query($sql,1);
	}

	function getSQL()
	{
		return $this->sql;
	}

	function insert($lane)
	{
		$sql="insert into `biocore`.`ngs_lanes`(`series_id`, `name`, `lane_id`, `facility`, `cost`,
					`date_submitted`, `date_received`, `phix_requested`,
					`phix_in_lane`, `total_samples`, `resequenced`, `notes`,
			`owner_id`, `group_id`, `perms`, `date_created`,
					`date_modified`, `last_modified_user`)
			values('".$this->model->series_id."','$lane->name','$lane->lane_id', '$lane->facility','$lane->cost',
					".$this->correct_date($lane->date_submitted).",".$this->correct_date($lane->date_received).",
			'$lane->phix_requested',
					'$lane->phix_in_lane','$lane->total_samples',
			".$this->correct_bool($lane->resequenced).",'$lane->notes',
			'".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
					now(), now(), '".$this->model->uid."');";
		$this->insert++;
		$this->sql=$sql;
		
		return $this->model->query($sql);
	}

	function update($lane)
	{
		$sql="UPDATE `biocore`.`ngs_lanes`
				SET
				`series_id` = '".$this->model->series_id."',
				`facility` = '$lane->facility',
				`lane_id` = '$lane->lane_id',
				`cost` = '$lane->cost',
				`date_submitted` = ".$this->correct_date($lane->date_submitted).",
				`date_received` = ".$this->correct_date($lane->date_received).",
				`phix_requested` = '$lane->phix_requested',
				`phix_in_lane` = '$lane->phix_in_lane',
				`total_samples` = '$lane->total_samples',
				`resequenced` = ".$this->correct_bool($lane->resequenced).",
				`notes` = '$lane->notes',
		`group_id`='".$this->model->gid."',
		`perms`='".$this->model->sid."',
		`date_modified`=now(),
		`last_modified_user`='".$this->model->uid."'
				where `id` = ".$this->getId($lane);
		$this->update++;

		return $this->model->query($sql);
	}

}


/* Protocol class */
class prot{}
class protocols extends main{
	private $prot_arr=[];

	function __construct($model, $prot_arr = [])
	{
		$this->prot_arr = $prot_arr;
		$this->model=$model;

		$this->processArr($prot_arr);
	}

	function getStat()
	{
		return "Update:$this->update, Insert:$this->insert";
	}

	function getId($prot)
	{
		$sql="select id from biocore.ngs_protocols where `name`='$prot->name'";
		return $this->model->query($sql,1);
	}
	function insert($prot)
	{
		$sql="insert into biocore.ngs_protocols(
				`name`, `growth`,
				`extraction`, `library_construction`, `crosslinking_method`,
				`fragmentation_method`, `strand_specific`, `library_strategy`,
				`owner_id`, `group_id`, `perms`,
				`date_created`, `date_modified`, `last_modified_user`)
				values
				('$prot->name', '$prot->growth',
				'$prot->extraction', '$prot->library_construction', '$prot->crosslinking_method',
				'$prot->fragmentation_method', '$prot->strand_specific', '$prot->library_strategy',
				'".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
				now(), now(), '".$this->model->uid."');";
		$this->insert++;
		return $this->model->query($sql);
	}

	function update($prot)
	{
		$sql="update biocore.ngs_protocols set
			`growth`='$prot->growth',
			`extraction`='$prot->extraction',
			`library_construction`='$prot->library_construction',
			`crosslinking_method`='$prot->crosslinking_method',
			`fragmentation_method`='$prot->fragmentation_method',
			`strand_specific`='$prot->strand_specific',
			`library_strategy`='$prot->library_strategy',
			`owner_id`='".$this->model->uid."',
			`group_id`='".$this->model->gid."',
			`perms`='".$this->model->sid."',
			`group_id`='".$this->model->gid."',
			`perms`='".$this->model->sid."',
			`date_modified`=now(),
			`last_modified_user`='".$this->model->uid."'
			where `id` = ".$this->getId($prot);
		$this->update++;

		return $this->model->query($sql);
	}
}


/* samples class */
class sample{}
class samples extends main{
	private $sample_arr=[];

	function __construct($model, $sample_arr = [])
	{
		$this->sample_arr=$sample_arr;
		$this->model=$model;

		$this->processArr($sample_arr);
	}

	function getSQL()
	{
		return $this->sql;
	}

	function getStat()
	{
		return "Update:$this->update, Insert:$this->insert";
	}

	function getId($sample)
	{
		$lane_id=$this->getLaneId($sample->lane_name);

		$sql="select id from biocore.ngs_samples where `name`='$sample->name' and `lane_id`='$lane_id' and `series_id`='".$this->model->series_id."'";
		return $this->model->query($sql,1);
	}
	function getLaneId($name)
	{
		$sql="select id from biocore.ngs_lanes where `name`='$name' and `series_id`='".$this->model->series_id."'";
		return $this->model->query($sql,1);
	}
	function getProtocolId($name)
	{
		$sql="select id from biocore.ngs_protocols where `name`='$name'";
		return $this->model->query($sql,1);
	}

	function insert($sample)
	{
		$lane_id=$this->getLaneId($sample->lane_name);
		$protocol_id=$this->getProtocolId($sample->protocol_name);

		$conds = explode(",", $sample->condition);
		$conds_symbs = explode(",", $sample->condition_symbol);
		$returned_ids = array();
		$returned_cond = array();
		
		$cond_check="SELECT `id`,`condition`
					FROM ngs_conds
					WHERE `condition` in (";
		foreach($conds as $c){
			if($c == end($conds)){
				$cond_check.="'$c'";
			}else{
				$cond_check.="'$c',";
			}
		}
		$cond_check.=");";
		$cond_check_result = json_decode($this->model->query($cond_check));
		foreach ($cond_check_result as $key => $object) {
			if(isset($returned_ids[0])){
				array_push($returned_ids, $object->id);
				array_push($returned_conditions, $object->condition);
			}else{
				$returned_ids[0] = $object->id;
				$returned_cond[0] = $object->condition;
			}
		}
		
		for($x = 0; $x < count($returned_cond); $x++){
			if(!in_array($conds[$x], $returned_cond)){
				$this->model->query("INSERT INTO `biocore`.`ngs_conds` (`cond_symbol`, `condition`) VALUES ('$conds_symbs[$x]', '$conds[$x]')");
				$new_cond_id = $this->model->query("SELECT `id` FROM `biocore`.`ngs_conds` WHERE cond_symbol = '$conds_symbs[$x]' AND condition = '$conds[$x]'");
				array_push($returned_ids, $new_cond_id);
			}
		}
		
		$sql="INSERT INTO `biocore`.`ngs_samples`
			(`series_id`, `protocol_id`, `lane_id`,
			`name`, `barcode`, `title`, `batch_id`,
			`source_symbol`, `source`, `organism`,
			`biosample_type`, `concentration`,
			`molecule`, `description`, `instrument_model`,
			`avg_insert_size`, `read_length`, `genotype`,
			`adapter`, `treatment_manufacturer`,
			`donor`, `time`, `biological_replica`,
			`spike_ins`,
			`technical_replica`, `notebook_ref`, `notes`,
			`owner_id`, `group_id`, `perms`, `date_created`,
			`date_modified`, `last_modified_user`)
			VALUES
			(
			'".$this->model->series_id."', '$protocol_id', '$lane_id',
			'$sample->name', '$sample->barcode', '$sample->title',
			'$sample->batch', '$sample->source_symbol', '$sample->source', '$sample->organism',
			'$sample->biosample_type', '$sample->concentration',
			'$sample->molecule', '$sample->description', '$sample->instrument_model',
			'$sample->avg_insert_size', '$sample->read_length', '$sample->genotype',
			'$sample->adapter', '$sample->treatment_manufacturer',
			'$sample->donor', '$sample->time', '$sample->biological_replica',
			'$sample->spikeins',
			'$sample->technical_replica', '$sample->notebook_ref', '$sample->notes',
			'".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
			now(), now(), '".$this->model->uid."');";
		$this->insert++;
		
		foreach($returned_ids as $id){
			if($this->model->query("SELECT `id` FROM `biocore`.`ngs_sample_conds` WHERE `sample_id` = '".$this->getId($sample)."' AND cond_id = $id") == "[]"){
				$this->model->query("INSERT INTO `biocore`.`ngs_sample_conds` (`sample_id`, `cond_id`) VALUES ('".$this->getId($sample)."', '$id')");
			}
		}
		
		$all_sample_cond = json_decode($this->model->query("SELECT `cond_id` FROM `biocore`.`ngs_sample_conds` WHERE `sample_id` = '".$this->getId($sample)."'"));
		foreach($all_sample_cond as $key => $object){
			if(!in_array($object->cond_id, $returned_ids)){
				$this->model->query("DELETE FROM `biocore`.`ngs_sample_conds` WHERE `sample_id` = '".$this->getId($sample)."' AND `cond_id` = ".$object->cond_id);
			}
		}
		
		return $this->model->query($sql);
	}

	function update($sample)
	{
		$lane_id=$this->getLaneId($sample->lane_name);
		$protocol_id=$this->getProtocolId($sample->protocol_name);
		
		$conds = explode(",", $sample->condition);
		$conds_symbs = explode(",", $sample->condition_symbol);
		$returned_ids = array();
		$returned_cond = array();
		
		$cond_check="SELECT `id`,`condition`
					FROM ngs_conds
					WHERE `condition` in (";
		foreach($conds as $c){
			if($c == end($conds)){
				$cond_check.="'$c'";
			}else{
				$cond_check.="'$c',";
			}
		}
		$cond_check.=");";
		$cond_check_result = json_decode($this->model->query($cond_check));
		foreach ($cond_check_result as $key => $object) {
			if(isset($returned_ids[0])){
				array_push($returned_ids, $object->id);
				array_push($returned_conditions, $object->condition);
			}else{
				$returned_ids[0] = $object->id;
				$returned_cond[0] = $object->condition;
			}
		}
		
		for($x = 0; $x < count($returned_cond); $x++){
			if(!in_array($conds[$x], $returned_cond)){
				$this->model->query("INSERT INTO `biocore`.`ngs_conds` (`cond_symbol`, `condition`) VALUES ('$conds_symbs[$x]', '$conds[$x]')");
				$new_cond_id = $this->model->query("SELECT `id` FROM `biocore`.`ngs_conds` WHERE cond_symbol = '$conds_symbs[$x]' AND condition = '$conds[$x]'");
				array_push($returned_ids, $new_cond_id);
			}
		}
		
		$sql="UPDATE `biocore`.`ngs_samples`
			SET
			`series_id` = '".$this->model->series_id."',
			`protocol_id` = '$protocol_id',
			`lane_id` = '$lane_id',
			`name` = '$sample->name',
			`barcode` = '$sample->barcode',
			`title` = '$sample->title',
			`batch_id` = '$sample->batch',
			`source_symbol` = '$sample->source_symbol',
			`source` = '$sample->source',
			`organism` = '$sample->organism',
			`biosample_type` = '$sample->biosample_type',
			`concentration` = '$sample->concentration',
			`molecule` = '$sample->molecule',
			`description` = '$sample->description',
			`instrument_model` = '$sample->instrument_model',
			`avg_insert_size` = '$sample->avg_insert_size',
			`read_length` = '$sample->read_length',
			`genotype` = '$sample->genotype',
			`adapter` = '$sample->adapter',
			`treatment_manufacturer` = '$sample->treatment_manufacturer',
			`donor` = '$sample->donor',
			`time` = '$sample->time',
			`biological_replica` = '$sample->biological_replica',
			`spike_ins` = '$sample->spikeins',
			`technical_replica` = '$sample->technical_replica',
			`notebook_ref` = '$sample->notebook_ref',
			`notes` = '$sample->notes',
			`group_id`='".$this->model->gid."',
			`perms`='".$this->model->sid."',
			`date_modified`=now(),
			`last_modified_user`='".$this->model->uid."'
			where `id` = ".$this->getId($sample);
		$this->update++;
		
		foreach($returned_ids as $id){
			if($this->model->query("SELECT `id` FROM `biocore`.`ngs_sample_conds` WHERE `sample_id` = '".$this->getId($sample)."' AND cond_id = $id") == "[]"){
				$this->model->query("INSERT INTO `biocore`.`ngs_sample_conds` (`sample_id`, `cond_id`) VALUES ('".$this->getId($sample)."', '$id')");
			}
		}
		
		$all_sample_cond = json_decode($this->model->query("SELECT `cond_id` FROM `biocore`.`ngs_sample_conds` WHERE `sample_id` = '".$this->getId($sample)."'"));
		foreach($all_sample_cond as $key => $object){
			if(!in_array($object->cond_id, $returned_ids)){
				$this->model->query("DELETE FROM `biocore`.`ngs_sample_conds` WHERE `sample_id` = '".$this->getId($sample)."' AND `cond_id` = ".$object->cond_id);
			}
		}
		return $this->model->query($sql);
	}
}



/* characteristics class */
class tag{}
class characteristics extends main{
	private $char_arr=[];

	function __construct($model, $char_arr = [])
	{
		$this->char_arr=$char_arr;
		$this->model=$model;

		$this->processArr($char_arr);
	}

	function getStat()
	{
		return "Update:$this->update, Insert:$this->insert";
	}

	function getSampleId($name)
	{
		$sql="select id from biocore.ngs_samples where `name`='$name' and `series_id`='$this->model->series_id'";
		return $this->model->query($sql,1);
	}

	function getId($tag)
	{
		$sample_id = $this->getSampleId($tag->sample_name);
		$sql="select id from biocore.ngs_characteristics where `sample_id` = $sample_id and `tag`='$tag->tag'";
		return $this->model->query($sql,1);
	}

	function insert($tag)
	{
		$sample_id = $this->getSampleId($tag->sample_name);
		$sql=" INSERT INTO `biocore`.`ngs_characteristics`(`sample_id`, `tag`,`value`,
		`owner_id`, `group_id`, `perms`, `date_created`, `date_modified`, `last_modified_user`)
				 VALUES('$sample_id','$tag->tag', '$tag->value',
		 '".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
		 now(), now(), '".$this->model->uid."');";
		$this->insert++;
		//return $sql;
		return $this->model->query($sql);
	}

	function update($tag)
	{
		$sql="update `biocore`.`ngs_characteristics` set `value`='$tag->value',
		`group_id`='".$this->model->gid."', `perms`='".$this->model->sid."',
		`date_modified`=now(), `last_modified_user`='".$this->model->uid."'
			where `id` = ".$this->getId($tag);
		$this->update++;

		return $this->model->query($sql);
	}
}


/* files class */
class file{}
class files extends main{
	private $files_arr=[];
	private $sample_arr=[];
	public $sample_id;
	private $lane_id;
	private $tablename;
	private $fieldname;
	private $value;
	private $dir_id;

	function __construct($model, $files_arr = [], $sample_arr=[])
	{
		$this->files_arr=$files_arr;
		$this->sample_arr=$sample_arr;
		$this->model=$model;

		$this->processArr($files_arr);
	}

	function getStat()
	{
		return "Update:$this->update, Insert:$this->insert";
	}

	function getLaneId($name)
	{
		$sql="select id from biocore.ngs_lanes where `name`='$name' and `series_id`='".$this->model->series_id."'";
		return $this->model->query($sql,1);
	}
	function getLaneIdFromSample($name){
		$lane_name=$this->sample_arr[$name]->lane_name;
		$sql="SELECT id FROM biocore.ngs_lanes where name='$lane_name' and `series_id`='".$this->model->series_id."'";
		return $this->model->query($sql,1);
	}
	function getSampleId($name)
	{
		$testsql="select id from biocore.ngs_lanes where `name`='$name' and `series_id`='".$this->model->series_id."'";
		$laneresult = $this->model->query($testsql,1);
		if($laneresult == '[]'){
			$lane_id=$this->getLaneIdFromSample($name);
			$sql="select id from biocore.ngs_samples where `name`='$name' and `lane_id`='$lane_id' and `series_id`='".$this->model->series_id."'";
			return $this->model->query($sql,1);
		}else{
			return 0;
		}
	}
	function getDirId($model)
	{
		$sql="select id from biocore.ngs_dirs where `fastq_dir`='$model->fastq_dir'";
		return $this->model->query($sql,1);
	}

	function getId($file)
	{
		$this->sample_id = $this->getSampleId($file->name);
		$this->lane_id = ($this->sample_id==0 ? $this->getLaneId($file->name) : $this->getLaneIdFromSample($file->name));
		$this->dir_id = $this->getDirId($this->model);
		if ($this->sample_id>0)
		{
			$this->tablename="ngs_temp_sample_files";
			$this->fieldname="sample_id";
		$this->value=$this->sample_id;
		
		$sql="select id from `biocore`.`$this->tablename` where `file_name`='$file->file_name' and `sample_id`='$this->sample_id'";
		return $this->model->query($sql,1);
		}
	else
	{
			$this->tablename="ngs_temp_lane_files";
			$this->fieldname="lane_id";
		$this->value=$this->lane_id;
		
		$sql="select id from `biocore`.`$this->tablename` where `file_name`='$file->file_name' and `lane_id`='$this->lane_id'";
		return $this->model->query($sql,1);
	}
		
	}

	function insert($file)
	{
		$sql="INSERT INTO `biocore`.`$this->tablename`
			(`file_name`,
			`$this->fieldname`, `dir_id`,
		`owner_id`, `group_id`, `perms`,
			`date_created`, `date_modified`,`last_modified_user`)
			VALUES
			('$file->file_name', '$this->value',
			'$this->dir_id', '".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
		now(), now(), '".$this->model->uid."');";
		$this->insert++;
		//return $sql;
		return $this->model->query($sql);
	}

	function update($file)
	{
		$sql="update `biocore`.`$this->tablename` set
			`fieldname`='$this->value', `dir_id`='$this->dir_id',
			`group_id`='".$this->model->gid."', `perms`='".$this->model->sid."',
		`date_modified`=now(), `last_modified_user`='".$this->model->uid."'
			where `id` = ".$this->getId($file);
		$this->update++;

		return $this->model->query($sql);
	}
}

/* diretories for the files class */
class dirs extends main{

	function __construct($model)
	{
		$this->model=$model;
	$this->process($this);
	}

	function getSQL()
	{
		return $this->sql;
	}

	function getStat()
	{
		return "Update:$this->update, Insert:$this->insert";
	}

	function getId()
	{
		$sql="select id from biocore.ngs_dirs where `fastq_dir`='".$this->model->fastq_dir."'";
		return $this->model->query($sql,1);
	}

	function insert()
	{

		$sql=" INSERT INTO `biocore`.`ngs_dirs`(`fastq_dir`,`backup_dir`, `amazon_bucket`,
		`owner_id`, `group_id`, `perms`,
		`date_created`, `date_modified`, `last_modified_user`)
				 VALUES('".$this->model->fastq_dir."', '".$this->model->backup_dir."', '".$this->model->amazon_bucket."',
		 '".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
		 now(), now(), '".$this->model->uid."');";
		$this->insert++;
	$this->sql=$sql;
		return $this->model->query($sql);
	}

	function update()
	{
		$sql="update `biocore`.`ngs_dirs` set
		`backup_dir`='".$this->model->backup_dir."', `amazon_bucket`='".$this->model->amazon_bucket."',
		`group_id`='".$this->model->gid."', `perms`='".$this->model->sid."',
		`date_modified`=now(), `last_modified_user`='".$this->model->uid."'
				where `id` = ".$this->getId();
		$this->update++;

		return $this->model->query($sql);
	}
}




?>
