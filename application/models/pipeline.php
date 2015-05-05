<?php
 
class Pipeline extends VanillaModel {

    function getId($value, $idfield, $table) {
            $result = $this->query("select DISTINCT $idfield from $table where `id`='$value'", 1);
            return $result;
    }

}