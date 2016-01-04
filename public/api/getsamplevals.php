<?php
error_reporting(E_ALL);
ini_set('report_errors','on');
require_once("../../config/config.php");
require_once("../../includes/dbfuncs.php");
require_once("class.csv-to-api.php");
$query = new dbfuncs();

#
# Sample Definitions:
# samples=sample_id1,sample_id2;run_id1:sample_id3,sample_id4;run_id3
# Examples:
# 1. Summary Example:
# API_PATH/api/getsamplevals.php?samples=8,10,11;6:13,14;7&file=counts/rRNA.summary.tsv&type=summary&format=html
#
#
# 2. Count Example:
# API_PATH/api/getsamplevals.php?samples=8,10,11;6:13,14;7&file=counts/rRNA.counts.tsv&common=id,len&key=id&format=html
#
#
# 3. RSEM Example:
# a. API_PATH/api/getsamplevals.php?samples=8,10,11;6:13,14;7&file=rsem/isoforms_expression_tpm.tsv&common=gene,transcript&key=transcript&format=html
# b. API_PATH/api/getsamplevals.php?samples=control_rep1,c_rep1,e_rep1&file=rsem/genes_expression_expected_count.tsv&common=gene,transcript&key=gene&type=rsem&format=html
# 
#
# 4. DESeq EXample:
# a.API_PATH/api/getsamplevals.php?samples=8,10,11;6:13,14;7&file=DESeq2RSEM1/alldetected_genes.tsv&common=name,&keepcols=padj,log2FoldChange&key=name&type=rsem&format=html
# b.API_PATH/api/getsamplevals.php?samples=8,10,11;6:13,14;7&file=DESeq2RSEM1/alldetected_genes.tsv&common=name,&keepcols=padj,log2FoldChange&key=name&type=rsem&format=html&filter=Fgf21,0610005C13Rik,Crebbp
#

$samples= $_GET['samples']; # Ex: samples=1:10,13:8

$format=$_GET['format']; # Ex: format:html [html|json|json2]
$file=$_GET['file']; # Ex: DESeq2RSEM1/alldetected_genes.tsv
$type=$_GET['type'];#summary or others
$commonfields=$_GET['common']; # Common fields that will hold the values from the first file
$keyfield=$_GET['key']; # index field specific to the table
$keepcols=$_GET['keepcols']; # This will keep the columns even they are common in the other files
$filter=$_GET['filter']; # This will be used to filter the values in the key field and report

$keepcols_array=explode(',', $keepcols);
$filter_array=explode(',', $filter);
#print $samplenames;

$data=$query->getSampleQuery($samples);


#header('Cache-Control: no-cache, must-revalidate');
#header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
#header('Content-type: application/json');

$dat=json_decode($data);
$sample_array=array();
for ($i=0; $i<sizeof($dat); $i++)
{
    $a[$dat[$i]->wkey].=$dat[$i]->samplename.",";
    array_push($sample_array, $dat[$i]->samplename);
}

$new_data=array();
$title=array();

foreach ($a as $i => $row)
{
   if ($type!="summary"){
       $fields="&fields=$commonfields,$keepcols,$row";
   }

   $url=API_PATH."/public/api/?source=".API_PATH."/public/pub/".$i."/$file&$fields&format=json";
   #print $url."<br><br>";
   $json = file_get_contents($url);
   #print $json."<br><br>";
   $dat1 = json_decode($json, $array=TRUE);
   #var_dump($dat1);
   foreach ($dat1[0] as $key => $value)
   {
      if (!in_array($key, $title) || in_array($key, $keepcols_array)){
        array_push($title, $key); 
      }
   }
   $new_data["title"]=$title;
   for ($j=0; $j<sizeof($dat1); $j++)
   {
       $row = $dat1[$j];
       if ($type=="summary")
       {
         if(in_array($row["File"], $sample_array))
         {
           $new_data[$row["File"]]=$row;
         }
       }
       else 
       {
          if ((isset($filter) && in_array($row[$keyfield], $filter_array)) || !isset($filter))
          {
          if(!isset($new_data[$row[$keyfield]])){
             $oc[$row[$keyfield]]=array();
             $new_data[$row[$keyfield]]=array();
          }
            foreach ($row as $key => $value)
            {
               if (!in_array($key, $oc[$row[$keyfield]]) || in_array($key, $keepcols_array) ){ 
                 array_push($new_data[$row[$keyfield]], $value);
                 array_push($oc[$row[$keyfield]] , $key);
               }
            }
          }
       }
   }
   #$obj = json_decode($json);
   #echo $obj."<br>";
}
#var_dump($new_data);
if($format == 'json2'){
    $format = 'json2_5';
}
$function = 'object_to_' . $format;
$api = new CSV_To_API();
echo $api->$function($new_data);


#print json_encode($new_data); 

#echo json_encode($data);
exit;
