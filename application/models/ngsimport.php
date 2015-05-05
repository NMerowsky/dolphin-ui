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

    function parseExcel($gid, $sid, $worksheet, $sheetData) {
        $this->worksheet=$worksheet;
        $this->sheetData=$sheetData;
	$this->sid=$sid;
	$this->gid=$gid;
	
        $text='<li>'.$this->worksheet['worksheetName'].'<br />';
                                
        $text.='Rows: '.$this->worksheet['totalRows'].' Columns: '.$this->worksheet['totalColumns'].'<br />';
        $text.='Cell Range: A1:'.$this->worksheet['lastColumnLetter'].$this->worksheet['totalRows'];
        $text.='</li>';
        
        $this->username=$_SESSION['user'];
        $sql="select id from biocore.users where `username`='$this->username'";
        $this->uid=$this->query($sql, 1);
	
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
	return $text;
        
    }
    
    function getMeta() {
        //var_dump($sheetData);
        $conts=[];
	
        for ($i=1;$i<=$this->worksheet['totalRows'];$i++)
        {
           if($this->sheetData[$i]["A"]=="title"){$experiment_name=$this->esc($this->sheetData[$i]["B"]);}
           if($this->sheetData[$i]["A"]=="summary"){$summary=$this->esc($this->sheetData[$i]["B"]);}
           if($this->sheetData[$i]["A"]=="overall design"){$design=$this->esc($this->sheetData[$i]["B"]);}
           if($this->sheetData[$i]["A"]=="contributor"){array_push($conts, $this->esc($this->sheetData[$i]["B"]));}
	   if($this->sheetData[$i]["A"]=="fastq directory"){$this->fastq_dir=$this->esc($this->sheetData[$i]["B"]);}
	   if($this->sheetData[$i]["A"]=="backup directory"){$this->backup_dir=$this->esc($this->sheetData[$i]["B"]);}
	   if($this->sheetData[$i]["A"]=="amazon bucket"){$this->amazon_bucket=$this->esc($this->sheetData[$i]["B"]);}
        }
        $new_series = new series($this, $experiment_name,$summary,$design);    
        $this->series_id=$new_series->getId();
        $text="SERIES:".$new_series->getStat()."<BR>";
        $new_conts = new contributors($this, $conts);
        $text.= "CONT:".$new_conts->getStat()."<BR>";
	if (isset($this->fastq_dir)){
	    $new_dirs = new dirs($this);
	    $text.= "DIRS:".$new_dirs->getStat()."<BR>";
	}

	return $text;
    }
    
    function getLanes(){
        $lane_arr = [];
        for ($i=4;$i<=$this->worksheet['totalRows'];$i++)
        {
           $lane = new lane();
           for ($j='A';$j<=$this->worksheet['lastColumnLetter'];$j++)
           {              
              if($this->sheetData[3][$j]=="Lane name"){$lane->name=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Lane id"){$lane->lane_id=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Sequencing facility"){$lane->facility=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Cost"){$lane->cost=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Date submitted"){$lane->date_submitted=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Date received"){$lane->date_received=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Total reads"){$lane->total_reads=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="% PhiX requested"){$lane->phix_requested=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="% PhiX in lane"){$lane->phix_in_lane=$this->esc($this->sheetData[$i][$j]);} 
              if($this->sheetData[3][$j]=="# of Samples"){$lane->total_samples=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Resequenced?"){$lane->resequenced=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Notes"){$lane->notes=$this->esc($this->sheetData[$i][$j]);}
           }
           if (!isset($lane->lane_id)){$lane->lane_id=0;}
           if($lane->name){$lane_arr[$lane->name]=$lane;}
        }
        //echo json_encode($lane_arr);
        
        $new_lanes = new lanes($this, $lane_arr);
        $text="LANE:".$new_lanes->getStat()."</br>";   
        #$text.="LANE:".$new_lanes->getSQL();   
	return $text;
    }
    
    function getProtocols(){
        $prot_arr = [];
        for ($i=4;$i<=$this->worksheet['totalRows'];$i++)
        {
           $prot = new prot();
           for ($j='A';$j<=$this->worksheet['lastColumnLetter'];$j++)
           {
              if($this->sheetData[3][$j]=="protocol name"){$prot->name= $this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="growth protocol"){$prot->growth= $this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="treatment protocol"){$prot->treatment= $this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="extract protocol"){$prot->extraction= $this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="library construction protocol"){$prot->library_construction= $this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="library strategy"){$prot->library_strategy= $this->esc($this->sheetData[$i][$j]);}

              if($prot->name){$prot_arr[$prot->name]=$prot;}
           }
        }
        //echo json_encode($prot_arr);
        
        $new_protocol = new protocols($this, $prot_arr);
        return "PROT:".$new_protocol->getStat();   
        //var_dump($sheetData);
    }
    
    function getSamples(){
        $sample_arr = [];
        $char_arr=[];
        for ($i=4;$i<=$this->worksheet['totalRows'];$i++)
        {
           $samp = new sample();
           $tag = new tag();
           for ($j='A';$j<=$this->worksheet['lastColumnLetter'];$j++)
           {
              if($this->sheetData[3][$j]=="Sample name"){$samp->name=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Lane name"){$samp->lane_name=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Protocol name"){$samp->protocol_name=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="barcode"){$samp->barcode=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="title"){$samp->title=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="source name"){$samp->source=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="organism"){$samp->organism=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="molecule"){$samp->molecule=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="description"){$samp->description=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="instrument model"){$samp->instrument_model=$this->esc($this->sheetData[$i][$j]);} 
              if($this->sheetData[3][$j]=="average insert size"){$samp->avg_insert_size=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="read length"){$samp->read_length=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Genotype"){$samp->genotype=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Condition"){$samp->condition=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Library type"){$samp->library_type=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="3' Adapter sequence"){$samp->adapter=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Notebook reference"){$samp->notebook_ref=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="Notes"){$samp->notes=$this->esc($this->sheetData[$i][$j]);}

              $valid = "/^characteristics:\s+?(.*)/";
              
              if(preg_match( $valid, $this->sheetData[3][$j], $matches) == 1)
              {
                if ($matches[1] != "tag" && $this->sheetData[$i][$j]!="")
                {
                  $tag->tag=$matches[1];
                  $tag->value=$this->sheetData[$i][$j];
                  $tag->sample_name=$samp->name;
                  $char_arr[$samp->name]=$tag;
                }
              }
              if($samp->name){$sample_arr[$samp->name]=$samp;}
           }
        }
        //echo json_encode($sample_arr);
        $this->samples=$sample_arr;
        $new_samples = new samples($this, $sample_arr);
        $text="SAMPLE:".$new_samples->getStat()."<BR>";
        $new_chars = new characteristics($this, $char_arr);
        $text.="CHAR:".$new_chars->getStat();
        return $text;
    }
    function getFiles(){
        $file_arr = [];
        for ($i=4;$i<=$this->worksheet['totalRows'];$i++)
        {
           $file = new file();
           for ($j='A';$j<=$this->worksheet['lastColumnLetter'];$j++)
           {
              if($this->sheetData[3][$j]=="Sample or Lane Name (Enter same name for multiple files)"){$file->name=$this->esc($this->sheetData[$i][$j]);}
              if($this->sheetData[3][$j]=="file name(comma separated for paired ends)"){$file->file_name=$this->esc($this->sheetData[$i][$j]);$file->file_name=preg_replace('/\s/', '', $file->file_name);}
              if($this->sheetData[3][$j]=="file checksum"){$file->checksum=$this->esc($this->sheetData[$i][$j]);}
              
           } 
           if($file->file_name){$file_arr[$file->file_name]=$file;}
        }
        //echo json_encode($file_arr);
        
        $new_files = new files($this, $file_arr, $this->samples);
        return "FILES:".$new_files->getStat();   
        //var_dump($sheetData);
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
    

    function __construct($model, $experiment_name, $summary, $design) 
    {
        $this->experiment_name=$experiment_name;
        $this->summary=$summary;
        $this->design=$design;
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
	      `owner_id`, `group_id`, `perms`, `date_created`, `date_modified`, `last_modified_user`)
              values('$this->experiment_name', '$this->summary', '$this->design',
	      '".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
	      now(), now(), '".$this->model->uid."');";
	      
        $this->insert++;
        return $this->model->query($sql);
    }

    function update()
    {
        $sql="update biocore.ngs_experiment_series set `summary`='$this->summary', `design`='$this->design',
	`group_id`='".$this->model->gid."', `perms`='".$this->model->sid."',
	`date_modified`=now(), `last_modified_user`='".$this->model->uid."' where `id` = ".$this->getId();
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
	$this->model=$model;
        
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
	`date_modified`=now(), `last_modified_user`='".$this->model->uid."'  where `id` =".$this->getId($val);
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
                   `date_submitted`, `date_received`, `total_reads`, `phix_requested`,
                   `phix_in_lane`, `total_samples`, `resequenced`, `notes`,
		   `owner_id`, `group_id`, `perms`, `date_created`,
                   `date_modified`, `last_modified_user`)
              values('".$this->model->series_id."',  '$lane->name',  '$lane->lane_id', '$lane->facility',  '$lane->cost',
                    ".$this->correct_date($lane->date_submitted).",  ".$this->correct_date($lane->date_received).",  
		    '$lane->total_reads',  '$lane->phix_requested',
                    '$lane->phix_in_lane',  '$lane->total_samples',  
		    ".$this->correct_bool($lane->resequenced).",  '$lane->notes',
		    '".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
                    now(), now(), '".$this->model->uid."');";
        $this->insert++;
	$this->sql=$sql;
        return $this->model->query($sql);
    }

    function update($lane)
    {
        $sql="  UPDATE `biocore`.`ngs_lanes`
                SET
                `series_id` = '".$this->model->series_id."',
                `facility` = '$lane->facility',
                `lane_id` = '$lane->lane_id',
                `cost` = '$lane->cost',
                `date_submitted` = ".$this->correct_date($lane->date_submitted).",
                `date_received` = ".$this->correct_date($lane->date_received).",
                `total_reads` = '$lane->total_reads',
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
        $sql="insert into biocore.ngs_protocols(`name`, `growth`, `treatment`,
                `extraction`, `library_construction`, `library_strategy`,
		`owner_id`, `group_id`, `perms`,
                `date_created`, `date_modified`, `last_modified_user`)
              values('$prot->name', '$prot->growth', '$prot->treatment',
                '$prot->extraction', '$prot->library_construction', '$prot->library_strategy',
		'".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
                now(), now(), '".$this->model->uid."');";
        $this->insert++;
        return $this->model->query($sql);
    }

    function update($prot)
    {
        $sql="update biocore.ngs_protocols set `growth`='$prot->growth',`treatment`='$prot->treatment',
            `extraction`='$prot->extraction', `library_construction`='$prot->library_construction',
            `library_strategy`='$prot->library_strategy',
	    `owner_id`='".$this->model->uid."', `group_id`='".$this->model->gid."', `perms`='".$this->model->sid."',
	    `group_id`='".$this->model->gid."', `perms`='".$this->model->sid."',
	    `date_modified`=now(), `last_modified_user`='".$this->model->uid."' 
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
        
        $sql="INSERT INTO `biocore`.`ngs_samples`
            (`series_id`, `protocol_id`, `lane_id`,
            `name`, `barcode`, `title`, `source`, `organism`,
            `molecule`, `description`, `instrument_model`,
            `avg_insert_size`, `read_length`, `genotype`,
            `condition`, `library_type`, `adapter`,
            `notebook_ref`, `notes`,
	    `owner_id`, `group_id`, `perms`, `date_created`,
            `date_modified`, `last_modified_user`)
            VALUES
            (
            '".$this->model->series_id."', '$protocol_id', '$lane_id',
            '$sample->name', '$sample->barcode', '$sample->title', '$sample->source', '$sample->organism',
            '$sample->molecule', '$sample->description', '$sample->instrument_model',
            '$sample->avg_insert_size', '$sample->read_length', '$sample->genotype',
            '$sample->condition', '$sample->library_type', '$sample->adapter',
            '$sample->notebook_ref', '$sample->notes',
	    '".$this->model->uid."', '".$this->model->gid."', '".$this->model->sid."',
	     now(), now(), '".$this->model->uid."');";
        $this->insert++;
        $this->insert=$sql;

        return $this->model->query($sql);
    }

    function update($sample)
    {
        $lane_id=$this->getLaneId($sample->lane_name);
        $protocol_id=$this->getProtocolId($sample->protocol_name);
        $sql="UPDATE `biocore`.`ngs_samples`
            SET
            `series_id` = '".$this->model->series_id."',
            `protocol_id` = '$protocol_id',
            `lane_id` = '$lane_id',
            `name` = '$sample->name',
            `barcode` = '$sample->barcode',
            `title` = '$sample->title',
            `source` = '$sample->source',
            `organism` = '$sample->organism',
            `molecule` = '$sample->molecule',
            `description` = '$sample->description',
            `instrument_model` = '$sample->instrument_model',
            `avg_insert_size` = '$sample->avg_insert_size',
            `read_length` = '$sample->read_length',
            `genotype` = '$sample->genotype',
            `condition` = '$sample->condition',
            `library_type` = '$sample->library_type',
            `adapter` = '$sample->adapter',
            `notebook_ref` = '$sample->notebook_ref',
            `notes` = '$sample->notes',
	    `group_id`='".$this->model->gid."',
	    `perms`='".$this->model->sid."',
	    `date_modified`=now(),
	    `last_modified_user`='".$this->model->uid."' 
            where `id` = ".$this->getId($sample);
        $this->update++;

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
        $sql=" INSERT INTO `biocore`.`ngs_characteristics`(`sample_id`, `tag`,  `value`,
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
        $lane_id=$this->getLaneIdFromSample($name);
        $sql="select id from biocore.ngs_samples where `name`='$name' and `lane_id`='$lane_id' and `series_id`='".$this->model->series_id."'";

        return $this->model->query($sql,1);
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
        }
	else
	{
           $this->tablename="ngs_temp_lane_files";
           $this->fieldname="lane_id";
	   $this->value=$this->lane_id;
	}
        $sql="select id from `biocore`.`$this->tablename` where `file_name`='$file->file_name' and `sample_id`='$this->sample_id'";
        return $this->model->query($sql,1);
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

        $sql=" INSERT INTO `biocore`.`ngs_dirs`(`fastq_dir`,  `backup_dir`, `amazon_bucket`,
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
