<?php
 
class NgsimportController extends VanillaController {
	private $username;
	function beforeAction() {

	}
 
	function index() {
		$this->username=$_SESSION['user'];
		$this->set('title','NGS Excel Import');
		$this->set('groups',$this->Ngsimport->getGroups($this->username));
        
        $this->set('uid', $_SESSION['uid']);
        $gids = $this->Ngsimport->getGroup($_SESSION['user']);
        $this->set('gids', $gids);
	}
	function process() {
            $this->set('uid', $_SESSION['uid']);
            $gids = $this->Ngsimport->getGroup($_SESSION['user']);
            $this->set('gids', $gids);
            
			$filename=$_FILES["excelFile"]["tmp_name"];
			$text = "Files size: ".$_FILES["excelFile"]["size"]."</br>";

			if($_FILES["excelFile"]["size"] > 0 && $_FILES["excelFile"]["size"]<500000)
			{

				//$target_file='tmp/files/'.$_FILES["excelFile"]["tmp_name"].'.xlsx';
				$target_file='../tmp/files/a.xlsx';
				move_uploaded_file($_FILES["excelFile"]["tmp_name"], $target_file);
				/** Include path **/
				set_include_path('../includes/excel/Classes/');

				/** PHPExcel_IOFactory */
				include 'PHPExcel/IOFactory.php';
				require_once '../includes/dbfuncs.php';

				$inputFileType = 'Excel5';
				$inputFileName = $target_file;
				$text.='Loading excel file <br />';
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);

				$text.='<hr />';

				$worksheetData = $objReader->listWorksheetInfo($inputFileName);
				$objPHPExcel = $objReader->load($inputFileName);

				$text.='<h3>Worksheet Information</h3>';
                $text.= '<h5>Checking for proper excel formatting:</h5><BR>';
				$text.='<ol>';
                
                $passed_final_check = true;
                
				$ngs=new Ngsimport();
				foreach ($worksheetData as $worksheet) {
					$objPHPExcel->setActiveSheetIndexByName($worksheet['worksheetName']);
					$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					$parseArray=$ngs->parseExcel($_POST["group_id"], $_POST["security_id"], $worksheet, $sheetData, $passed_final_check);
                    $passed_final_check = $parseArray[0];
                    $text.=$parseArray[1];
				}
                $text.= '</ol>';
                
                if($passed_final_check){
                    $text.= '<h5>Updating/Inserting the database</h5><BR>';
                    $text.= '<ol>';
                    foreach ($worksheetData as $worksheet) {
                        $objPHPExcel->setActiveSheetIndexByName($worksheet['worksheetName']);
                        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
                        $text.=$ngs->finalizeExcel($worksheet, $sheetData);
                    }
                    $text.='</ol>';
                    
                    $text.='<h4>We are currently processing your samples to obtain read counts and additional information.<br>
                            You can check the status of these initial runs on your NGS Status page.</h4><br>';
                    $text.= '<div>
                            <input type="button" class="btn btn-primary" value="Send to Status" onclick="sendToStatus()">
                            </div>';
                }else{
                    $text.=$ngs->errorText("<BR><BR>Excel import aborted due to errors, see above<BR>");
                }
			}
			$this->set('mytext', $text);
	}

	function afterAction() {

	}

}
