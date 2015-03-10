<?php
 
class SearchController extends VanillaController {

    function beforeAction() {

    }
 
    function index() {
        $this->set('field', "Search");
        $this->set('assay', $this->Search->getAccItems("library_type", "ngs_samples"));
        $this->set('organism', $this->Search->getAccItems("organism", "ngs_samples"));
        $this->set('molecule', $this->Search->getAccItems("molecule", "ngs_samples"));
        $this->set('source', $this->Search->getAccItems("source", "ngs_samples"));
        $this->set('genotype', $this->Search->getAccItems("genotype", "ngs_samples"));
    }
    
    function browse($field, $value) {
        $this->index();
        $this->set('field', $field);
        $this->set('value', $value);
    }
    function details($table, $value) {
        $this->index();
        $this->set('table', $table); 
        if ($table=='experiment_series')
        {
           $this->set('experiment_series', $this->Search->getValues($value, 'ngs_experiment_series'));
           $this->set('experiment_series_fields', $this->Search->getFields('ngs_experiment_series'));
        }
        if ($table=='experiments')
        {
           $this->set('experiment_series_fields', $this->Search->getFields('ngs_experiment_series'));
           $this->set('experiment_fields', $this->Search->getFields('ngs_lanes'));
           
           $this->set('experiment_series', $this->Search->getValues($this->Search->getId($value, 'series_id', 'ngs_lanes'), 'ngs_experiment_series'));
           $this->set('experiments', $this->Search->getValues($value, 'ngs_lanes'));
        }
        if ($table=='samples')
        {
           $this->set('experiment_series_fields', $this->Search->getFields('ngs_experiment_series'));
           $this->set('experiment_fields', $this->Search->getFields('ngs_lanes'));
           $this->set('sample_fields', $this->Search->getFields('ngs_samples'));
           
           $this->set('experiment_series', $this->Search->getValues($this->Search->getId($value, 'series_id', 'ngs_samples'), 'ngs_experiment_series'));
           $this->set('experiments', $this->Search->getValues($this->Search->getId($value, 'lane_id', 'ngs_samples'), 'ngs_lanes'));
           $this->set('samples', $this->Search->getValues($value, 'ngs_samples'));
        }
    }


    function afterAction() {

    }

}
