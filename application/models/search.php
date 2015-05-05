<?php
 
class Search extends VanillaModel {
 
  /** Get menuitems for this user **/
    function getAccItems($fieldname, $tablename) {
        $result = $this->query("select $fieldname name, count($fieldname) count from $tablename where $fieldname!='' group by $fieldname");
        return json_decode($result, true);
    }
    function getAccItemsCont($fieldname, $tablename, $search){
        if($search != ""){
            $advQuery = "";
            foreach(explode('$', $search) as $s){
                $s = urldecode($s);
                $split = explode('=', $s);
                $advQuery.= " AND " . $split[0]. " = " . '"'. $split[1] . '"';
            }
            $result = $this->query("select $fieldname name, count($fieldname) count from $tablename where $fieldname!='' $advQuery group by $fieldname");
        }
        else{
            $result = $this->query("select $fieldname name, count($fieldname) count from $tablename where $fieldname!='' group by $fieldname");
        }
        return json_decode($result, true);
    }
    function getId($value, $idfield, $table) {
        $result = $this->query("select DISTINCT $idfield from $table where `id`='$value'", 1);
        return $result;
    }
    function getValues($value, $table) {
        $result = $this->query("select * from $table where `id`='$value'");
        return json_decode($result, true);
    }
    function getFields($table){
        $result = $this->query("select df.fieldname, df.title from datatables dt, datafields df where df.table_id=dt.id and dt.tablename='$table'");
        return json_decode($result, true);
    }
}
