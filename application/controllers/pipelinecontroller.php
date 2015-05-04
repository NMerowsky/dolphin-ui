<?php
 
class PipelineController extends VanillaController {

    function beforeAction() {

    }
 
    function index() {
        $this->set('field', "Pipeline");
    }
    
    function selected($selection) {
        $this->set('field', "Selected");
        $this->set('selection', $selection);
    }
    
    function status(){
        $this->set('field', 'Status');
    }
    
    function rerun($run_id, $selection){
        $this->set('selection', $selection);
        $this->set('run_id', $run_id);
        $this->set('field', 'Status');
    }
    
    function report($run_id, $selection){
        $this->set('run_id', $run_id);
        $this->set('selection', $selection);
        $this->set('field', 'Status');
    }
    
    function fastlane($selection){
        $this->set('field', 'Fastlane');
        $this->set('selection', $selection);
    }
    
    function plots($selection){
        $this->set('field', 'Plots');
        $this->set('selection', $selection);
    }
    
    function afterAction() {

    }

}
