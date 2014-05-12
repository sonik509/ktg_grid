<?php

class ktg_grid_sql {
	
		protected $user="root";
        protected $server="localhost";
        protected $pass="";
        protected $database="test";


	function sql2_rec_update($table,$query,$cols_upd){
		$query=mysql_query("UPDATE ".$table." SET $cols_upd $query");
		$thissuq->query=$query;
		return $thissuq;
	}
	
	function insertBefore($input, $index, $element) {
		if (!array_key_exists($index, $input)) {
			array_push($input,$element);
		}else{
		$tmpArray = array();
		$originalIndex = 0;
		foreach ($input as $key => $value) {
		if ($key === $index) {
		$tmpArray[] = $element;
		break;
		}
		$tmpArray[$key] = $value;
		$originalIndex++;
		}
		array_splice($input, 0, $originalIndex, $tmpArray);
                }
		return $input;
                
	}
        
        function insertAfter($input, $index, $element) {
            if (!array_key_exists($index, $input)) {
                throw new Exception("Index not found");
            }
            $tmpArray = array();
            $originalIndex = 0;
            foreach ($input as $key => $value) {
                $tmpArray[$key] = $value;
                $originalIndex++;
                if ($key === $index) {
                    $tmpArray[] = $element;
                    break;
                }
            }
            array_splice($input, 0, $originalIndex, $tmpArray);
            return $input;
        }
	
	function sql3_record_query($query,$order,$limit,$key) {
		$total_rec=0;
		$table=null;
		$order_by=null;
		if(!empty($order)){
		$order_by="Order By $order";
		}
		if(!empty($limit)){
		$limit_q="LIMIT $limit";
		}
			
		$table=$table;
		$field_array=array();
		
		$array_rec=array();
		$array_rec_flip = array();
		//echo "select $query $order_by $limit_q"."<br>";
		$query=@mysql_query("select $query $order_by $limit_q");
		if (mysql_error()!=null){
		echo mysql_error();
		exit();
		}
		$total_fields=mysql_num_fields($query);
		for($say1=0; $say1<$total_fields; $say1++){
		$field=mysql_field_name($query,$say1);
		array_push($field_array,$field);
		}
		$total_fields=$total_fields+count($key);
		for($a=0; $a<count($key); $a++){
		$field_array=$this->insertBefore($field_array, $key[$a], $key[$a]);
		}
		while ($dizi=mysql_fetch_row($query)){
		if($key>0){
		for($a=0; $a<count($key); $a++){
		$dizi=$this->insertBefore($dizi, $key[$a], $key[$a]);
		}
		}
		array_push($array_rec,$dizi);
		$total_rec++;
		}
		
		//flip multidimensional array
		foreach ($array_rec as $key => $subarr) {
		foreach ($subarr as $subkey => $subvalue) {
		$array_rec_flip[$subkey][$key] = $subvalue;
		 }
		}
		//
		$ths = new stdClass(); 
		$ths->array_rec=$array_rec_flip;
		$ths->total_fields=$total_fields;
		$ths->total_rec=$total_rec;
		$ths->field_array=$field_array;					
		return $ths;
	}
	
	function sql2_table_query($table,$query,$field)
	{
		if(!empty($query)){
		$where_q="where ".$query;
		}
		//echo "SELECT $field from ".$table." ".$where_q;
		$query=mysql_query("SELECT $field from ".$table." ".$where_q);
		$query2=@mysql_fetch_array($query);		
		//$txt->query_result=$query2[$field];
		return $query2[$field];
	}
	
	////////////////////////
	function sql3_row_query($query)
	{
		$row_count=@mysql_num_rows(mysql_query("select $query"));	
		return $row_count;
	}
	////////////////////////
	
	
	function sql2_table_ins($table,$cols,$cols_data)
	{
		$query=mysql_query("INSERT INTO ".$table." ($cols) VALUES($cols_data)");
		//echo ("INSERT INTO ".$table." ($cols) VALUES($cols_data)");
		//echo mysql_error();
		$id = mysql_insert_id();
		$cat_ins->query=$query;
		$cat_ins->id=$id;
		return $cat_ins;		
	}
        

	function sql2_rec_delete($table,$query)
	{
		if(!empty($query)){
		$where_q="where ".$query;
		}
		//echo "DELETE FROM ".$table." $where_q";
		$query=mysql_query("DELETE FROM ".$table." $where_q");
		$cat_del->query=$query;
		return $cat_del;
	}
	
}

?>
