<?php
session_start();
?>   
<?php

include 'ktg_grid_sql.php';

class ktg_grid extends ktg_grid_sql{

    protected $num_show=false;
    protected $hover_style=null;
    protected $checked_style=null;
    protected $zebra_style_css=null;
    protected $row_fix=false;
    protected $change_field=array();
    protected $order;
    protected $vir_col_key;
    protected $col_titles;
    protected $row_limit;
    protected $set_js=array('checked_hl'=>true,'rowclickchecked'=>true);
    protected $set_style=array('zebra_style'=>true,'hover_hl'=>true);
    protected $cond_field=array();


    function __construct($query,$ranKey)
    {
    $this->ranKey=$ranKey;
    if (empty($_POST['ktg_cont'])){   
    unset($_SESSION['ktgg_query_'.$ranKey]);
    unset($_SESSION['ktgg_set_field_'.$ranKey]);
    unset($_SESSION['ktgg_order_'.$ranKey]);
    unset($_SESSION['ktgg_vir_col_key_'.$ranKey]);
    unset($_SESSION['ktgg_row_limit_'.$ranKey]);
    unset($_SESSION['ktgg_hidden_'.$ranKey]);
    unset($_SESSION['ktgg_title_'.$ranKey]);
    unset($_SESSION['ktgg_hover_hl_'.$ranKey]);
    unset($_SESSION['ktgg_checked_hl_'.$ranKey]);
    unset($_SESSION['ktgg_zebrastyle_'.$ranKey]);
    unset($_SESSION['ktgg_set_style_'.$ranKey]);
    unset($_SESSION['ktgg_set_js_'.$ranKey]);
    unset($_SESSION['ktgg_row_fix_'.$ranKey]);
    unset($_SESSION['ktgg_num_show_'.$ranKey]);
    unset($_SESSION['cond_field_'.$ranKey]);
    }	
	$dir=str_replace("\\","/",dirname(__FILE__));
	$path2=str_replace($_SERVER['DOCUMENT_ROOT'], "//".$_SERVER['HTTP_HOST'], $dir);
    $this->db=@mysql_connect ($this->server,$this->user,$this->pass)
    or die ("Database connection error");
    @mysql_select_db($this->database)
    or die("Table connection error");
    mysql_query("SET NAMES utf8");
    mysql_query("SET CHARACTER SET utf8");
    mysql_query("SET COLLATION_CONNECTION = 'utf8_general_ci'");
    $_SESSION['ktgg_query_'.$this->ranKey]=$query;
    $this->query=$query;
    echo '<style>
    #ktg_cont_'.$ranKey.' input,#ktg_cont_'.$ranKey.' label{
    vertical-align: baseline;
    margin: 0;
    padding: 0;
    border: 0;
    display:inline;
    clear: both;
    position: static;
    }
    </style>';
    echo '<script>
    function doSomething_'.$this->ranKey.'(id,key){
    if (key=="next"){
    document.getElementById(id).value++;
    }
    if (key=="back"){
    document.getElementById(id).value--;
    }
    if (key=="first"){
    document.getElementById(id).value="1";
    }
    if (key=="end"){
    document.getElementById(id).value=document.getElementById("max_page_'.$this->ranKey.'").value;
    }
    if (key=="go"){
    document.getElementById(id).value=document.getElementById("cur_page_'.$this->ranKey.'").value;
    }
    ktg_page'.$this->ranKey.'(document.getElementById(id).value);
    }
    function ktg_page'.$this->ranKey.'(value){
    $.post("'.$path2.'/ktg_grid.php?key='.$ranKey.'",{"ktg_cont":value},function(res){
    $("#ktg_cont_'.$this->ranKey.'").html(res);
    });
    }</script>';
	//echo $dir;	
    return $this->query;	
    }
	

	
    function set_order($order){
    $_SESSION['ktgg_order_'.$this->ranKey]=$order;
    $this->order=$order;
    return $this->order;
    }


    function set_table_fix_hg($st){
    $this->row_fix=$st;
    $_SESSION['ktgg_row_fix_'.$this->ranKey]=$st;
    }

    function set_col_title ($col_titles){
    $this->col_titles[]=$col_titles;
    $_SESSION['ktgg_title_'.$this->ranKey]=$this->col_titles;
    }

    function add_column($number){
    $this->vir_col_key[]=$number;
    $_SESSION['ktgg_vir_col_key_'.$this->ranKey]=$this->vir_col_key;
    }

    function set_col_hidden($col_hidden){
    $this->col_hidden[]=$col_hidden;
    $_SESSION['ktgg_hidden_'.$this->ranKey]=$this->col_hidden;
    //return $this->col_hidden;
    }

    function set_row_limit($limit){
    $this->row_limit=$limit;
    $_SESSION['ktgg_row_limit_'.$this->ranKey]=$limit;
    return $this->row_limit;
    }

    function set_col_num_show($st){
    $this->num_show=$st;
    $_SESSION['ktgg_num_show_'.$this->ranKey]=$st;
    }

    function set_fields($change_field,$obj){   
    $array1=array($obj);
    $this->change_field[$change_field]=$array1;
    $_SESSION['ktgg_set_field_'.$this->ranKey]=$this->change_field;
    }

    function set_text($key,$text){
    $this->set_text[$key]=$text;
    $_SESSION['ktgg_set_text_'.$this->ranKey]=$this->set_text;
    }

    function set_style($class,$att){
    $this->set_style[$class]=$att;
    $_SESSION['ktgg_set_style_'.$this->ranKey]=$this->set_style;
    }

    function set_js($func,$att){
    $this->set_js[$func]=$att;
    $_SESSION['ktgg_set_js_'.$this->ranKey]=$this->set_js;
    }

    protected function set_field($data,$values){
        $vals=array();
            $str1=explode("%%", $data);
            foreach ($str1 as $str1a){
               if($str1a!=null){
                  $vals[]=$str1a; 
               } 
            }

            $new_val=$data;
            foreach ($vals as $val){
                if(is_numeric($val)){
                $new_val=str_replace("%%$val%%", $values[$val], $new_val);
                }else{
                $new_val=str_replace("%%$val%%", $val, $new_val);    
                }
            }
            return $new_val;  
    }
            
    function set_field_logic($test_field,$opr,$data,$chan_col,$new_data){    
    $carray=array($opr,$data,$new_data,$test_field);
    $this->cond_field[][$chan_col]=$carray;
    $_SESSION['cond_field_'.$this->ranKey]=$this->cond_field;           
    }
    
	protected function getAll2Keys($array_val){
        $result = array();
        $firstKeys = array_keys($array_val);
        for($i=0;$i<count($firstKeys);$i++){
            $key = $firstKeys[$i];
            $result = array_merge($result,array_keys($array_val[$key]));
        }
        return $result;
    }
	
    protected function  num_cond ($var1, $opr, $var2) {
    switch ($opr) {
    case "==": return $var1 == $var2;
    case "!=": return $var1 != $var2;
    case ">=": return $var1 >= $var2;
    case "<=": return $var1 <= $var2;
    case ">":  return $var1 >  $var2;
    case "<":  return $var1 <  $var2;
    default:   return false;
    }   
    }
        
    protected function conditional_field_set($value,$key,$values){
    $res=null;
    foreach ($this->cond_field as $cond_fields){     
    $opr=$cond_fields[$key][0];
    $data=$cond_fields[$key][1];
    $new_data=$cond_fields[$key][2];
    $test_field=$values[$cond_fields[$key][3]];
    if($this->num_cond ($test_field, $opr, $data)){
        $vals=array();
            $str1=explode("%%", $new_data);
            foreach ($str1 as $str1a){
               if($str1a!=null){
                  $vals[]=$str1a; 
               } 
            }

            $new_val=$new_data;
            foreach ($vals as $val){        
               if(is_numeric($val)){           
               $new_val=str_replace("%%$val%%", $values[$val], $new_val);
               }else{
               $new_val=str_replace("%%$val%%",$val, $new_val);
               }               
            }
    $res=$new_val;
    
    }
    }
    if (empty($res)){
    $res=$value; 
    }
    return $res;
    }
                
    protected function style_load(){
	echo'<style>';
	if (empty($this->set_style['grid_main'])){
	echo'.ktg_grid_main_'.$this->ranKey.'{
	font: bold 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	}';
	}else{
	echo'.ktg_grid_main_'.$this->ranKey.'{
	'.$this->set_style['grid_main'].'
	}';
	}

	if (empty($this->set_style['grid_footer'])){
	echo'.ktg_grid_footer_'.$this->ranKey.'{
	background: -webkit-linear-gradient(#EEEEEE, #CCCCCC);
	background: -moz-linear-gradient(bottom, #CCCCCC, #EEEEEE 25px);
	background: -ms-linear-gradient(#EEEEEE, #CCCCCC);
	height: 30px;
	}';
	}else{
	echo'.ktg_grid_footer_'.$this->ranKey.'{
	'.$this->set_style['grid_footer'].'
	}';
	}
	
	if (empty($this->set_style['grid_footer'])){
	echo'.ktg_grid_footer_'.$this->ranKey.' button,.ktg_grid_footer_'.$this->ranKey.' input{
	font: bold 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
        padding:2px;
            }';
        echo '.ktg_grid_footer_'.$this->ranKey.' label{
        font: bold 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
        color: #474747;   
        }';
	}else{
	echo'.ktg_grid_footer_'.$this->ranKey.' button,.ktg_grid_footer_'.$this->ranKey.' input,.ktg_grid_footer_'.$this->ranKey.' label{
	'.$this->set_style['grid_footer'].'
	}';
	}

	if (empty($this->set_style['cur_page'])){
	echo'#cur_page_'.$this->ranKey.' {
	width:50px;
    height:20px;
	text-align:center;
	}';
	}else{
	echo'.ktg_cur_page_'.$this->ranKey.'{
	'.$this->set_style['cur_page'].'
	}';
	}
	
	if (empty($this->set_style['cur_page'])){
	echo '.ktg_grid_tbody_'.$this->ranKey.'{
	color: #474747;
	font:11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	}';
	}else{
	echo '.ktg_grid_tbody_'.$this->ranKey.'{
	'.$this->set_style['cur_page'].'
	}';
	}
	if (empty($this->set_style['row_height'])){
	echo '
	#ktg_grid_table_'.$this->ranKey.' tr {		
	line-height: 30px;	
	}';
	}else{
	echo '
	#ktg_grid_table_'.$this->ranKey.' tr {		
	line-height: '.$this->set_style['row_height'].'px;	
	}';
	}
	if (empty($this->set_style['grid_tbody'])){
	echo ".ktg_grid_tbody_".$this->ranKey." td{
	border-bottom:1px solid #E0E0E0;
	border-right:1px solid #E0E0E0;
	padding-left:6px;
	vertical-align: middle; 
	}";
	}else{
	echo ".ktg_grid_tbody_".$this->ranKey." td{
	".$this->set_style['grid_tbody']."
	}";
	}
	
	if (empty($this->set_style['td_input'])){
	echo '.ktg_grid_tbody_'.$this->ranKey.' td input {
		  float: left;
		  margin: 0 auto;
		  width: 100%;
		  }';
	}else{
	echo '.ktg_grid_tbody_'.$this->ranKey.' td input {
		  '.$this->set_style['td_input'].'
		  }';
	}

	
	echo '.ktg_grid_tbody_'.$this->ranKey.' td.first {
	border-left: 1px solid #E0E0E0;
	}';
	

	if (empty($this->set_style['grid_table'])){
	echo '
	#ktg_grid_table_'.$this->ranKey.' {
	border-collapse: collapse;
	width: 100%;		
	}';
	}else{
	echo '
	#ktg_grid_table_'.$this->ranKey.' {
	'.$this->set_style['grid_table'].'	
	}';	
	}

	if (empty($this->set_style['grid_table_head'])){
	echo '
	#ktg_grid_table_'.$this->ranKey.' th {		
        font: bold 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
        color: #474747;
        border-bottom:1px solid #E0E0E0;
        border-right:1px solid #E0E0E0;
        border-top:1px solid #E0E0E0;
        letter-spacing: 2px;
        text-align: center;
        padding-left: 6px;
        background: -webkit-linear-gradient(#EEEEEE, #CCCCCC);
        background: -moz-linear-gradient(bottom, #CCCCCC, #EEEEEE 25px);
        background: -ms-linear-gradient(#EEEEEE, #CCCCCC);
        line-height:25px;	
	}';
	}else{
	echo '
	#ktg_grid_table_'.$this->ranKey.' th {		
	'.$this->set_style['grid_table_head'].'	
	}';
	}
	
		
	if ($this->set_style['hover_hl']){
	if (empty($this->set_style['hover_odd'])){
	echo '
	.ktg_grid_tbody_'.$this->ranKey.' tr:hover,.ktg_grid_tbody_'.$this->ranKey.' tr.odd:hover,.ktg_grid_tbody_'.$this->ranKey.' tr.even:hover{
	background-color: #fff884;
	color: #000;
	}
	';
	}else{
	echo '
	.ktg_grid_tbody_'.$this->ranKey.' tr:hover,.ktg_grid_tbody_'.$this->ranKey.' tr.odd:hover,.ktg_grid_tbody_'.$this->ranKey.' tr.even:hover{
	'.$this->set_style['hover_odd'].'
	}
	';
	}
	}


	if($this->set_style['zebra_style']){
	if(empty($this->set_style['row_odd'])){
	echo "
	.ktg_grid_tbody_".$this->ranKey." tr.odd{
	background-color: #f3f3f3;
	}
	";
	}else{
	echo "
	.ktg_grid_tbody_".$this->ranKey." tr.odd{
	".$this->set_style['row_odd']."
	}";
	}
    
	if(empty($this->set_style['row_even'])){
	echo "
	.ktg_grid_tbody_".$this->ranKey." tr.even{
	background-color: #fff;
	}
	";
	}else{
	echo "
	.ktg_grid_tbody_".$this->ranKey." tr.even{
	".$this->set_style['row_odd']."
	}";
	}
        }            
	echo "</style>";
	
	if($this->set_js['checked_hl']){
		if (empty($this->set_style['checked_odd'])){
		echo '<script>
		$(document).ready(function() {
			$("#ktg_grid_table_'.$this->ranKey.' input").click(function() {
				if ($(this).is(":checked")) {
					$(this).parent().parent().css({"background-color":"#fff884","color":"#000"});	
				} else {
					$(this).parent().parent().css("background-color","");
					$(this).parent().parent().css("color","");
				}
			});
		});	</script>';
		}else{
		$tag = str_replace(":", '":"', $this->set_style['checked_odd']);
		$tag = str_replace(";", '","', $tag);
		$tag = str_replace(" ", "", $tag);
		echo '<script>
		$(document).ready(function() {
			$("#ktg_grid_table_'.$this->ranKey.' input").click(function() {
				if ($(this).is(":checked")) {
					$(this).parent().parent().css({"'.$tag.'"});	
				} else {
					$(this).parent().parent().css("background-color","");
					$(this).parent().parent().css("color","");
				}
			});
		});
		</script>';
		}
		}
	
    }
	
	
    protected function will_run_before(){
        if ($this->set_js['rowclickchecked']){
	echo "<script>
	$(document).ready(function() {
	$('#ktg_grid_table_".$this->ranKey." tr').click(function(event) {
	if (event.target.type !== 'checkbox') {
	$(':checkbox', this).trigger('click');
	}
	});
	});
	</script>";
        }		
	$this->style_load();		
    }
	
    function show(){
	$this->will_run_before();
	
	echo '<div class="ktg_class_main_'.$this->ranKey.'" id="ktg_cont_'.$this->ranKey.'">';
	if(!empty($_POST['ktg_cont'])){
	$page=$_POST['ktg_cont'];
	}else{
	$page=null;
	}
	if (empty($page)){
	$page=1;
	}
	$limit=$this->row_limit;
	if(empty($limit)){
	$limit=10;
	}
	//sql den kayıt sayısını al
	$row_count=$this->sql3_row_query($this->query);
	$total_page=ceil($row_count / $limit);
	$start=($page-1) * $limit;
	$limit="$start,$limit";
	$chan_fields=array();
	//sql sorgusu
	
	$stl=$this->sql3_record_query($this->query,$this->order,$limit,$this->vir_col_key);
	$field_arrays=$stl->field_array;
           
    foreach ($stl->array_rec as $key => $subarr) {
	foreach ($subarr as $subkey => $subvalue) {
	$array_rec_flip[$subkey][$key] = $subvalue;
	}
	} 
       
	
	   
	$col_titlesc=array();
	if ($this->col_titles){
	foreach($this->col_titles as $col_titles_a){	
	$col_titles_b=explode('=>',$col_titles_a);
	$col_titlesc[$col_titles_b[0]]=$col_titles_b[1];
	}
	}
	

	if(isset($this->col_hidden)){
	$hidden_fields=$this->col_hidden;
	}else{
	$hidden_fields=array();
	}
	
	$th_class=null;
	$td_class=null;
	$td_dif=null;
  
	echo '<table id="ktg_grid_table_'.$this->ranKey.'">';
	echo '<thead><tr>';

	
	
	foreach ($col_titlesc as $key=>$value){
	array_splice($field_arrays, $key, 1, $value);
	}
	

	for ($say3=0; $say3<count($field_arrays); $say3++){
	
        foreach ($hidden_fields as $hidden_field){
            if($say3==$hidden_field){
              $say3++;  
            }
        }
        
        //sütun gizlemekten kaynaklanan undefined offset hatasını önle
        if($say3==count($field_arrays)){
            break;
        }
        
	echo '<th>';
	echo $field_arrays[$say3];
	if($this->num_show){
	echo " ( $say3 )";
	}
	echo '</th>';
	}
	echo ' </tr></thead>';
  
	
	echo '<tbody class="ktg_grid_tbody_'.$this->ranKey.'">';
	for ($say1=0; $say1<$stl->total_rec; $say1++){
	if ($td_dif=='1'){
	$th_class='class="first"';
	$tr_class='class="even"';
	}else{
	$th_class='class="first"';
	$tr_class='class="odd"';
	}


	echo '<tr '.$tr_class.'>';
	$color=0;
	for ($say2=0; $say2<$stl->total_fields; $say2++){	
	$color++;	       
        foreach ($hidden_fields as $hidden_field){
            if($say2==$hidden_field){
              $say2++;  
            }
        }
        
        //sütun gizlemekten kaynaklanan undefined offset hatasını önle
        if($say2==count($field_arrays)){
            break;
        }
        
	if ($color==1){
	$class=$th_class;
	}else{
	$class=$td_class;
	}
	echo '<td '.$class.' >';	
	
	
	if (in_array($say2, array_keys($this->change_field))){
        echo $this->set_field($this->change_field[$say2][0],$array_rec_flip[$say1]);       
	}elseif(in_array($say2, array_values($this->getAll2Keys($this->cond_field)))){
        echo $this->conditional_field_set($stl->array_rec[$say2][$say1],$say2,$array_rec_flip[$say1]);	
	}else{
        echo $stl->array_rec[$say2][$say1];   
        }	
	echo '</td>';
	}
	echo '</tr>';
	if ($td_dif=='1'){
	$td_dif='0';
	}else{
	$td_dif='1';
	}
	}
	
	$next_btn_false=null;
	$back_btn_false=null;
	if ($page>=$total_page){
	$next_btn_false="disabled";
	}
	if ($page<=1){
	$back_btn_false="disabled";
	}
	
        //yükseklik sabitleme aktifse boş alanları boş kutularla doldur
	if ($this->row_fix){
	if($stl->total_fields==$say2){
	$test=$this->row_limit-$stl->total_rec;
	if ($test>0){
	for ($a=0; $a<$test; $a++){
	echo '<tr><td class="first" style="background-color: #fff;  border-color:#fff;" colspan="'.count($field_arrays).'">&nbsp;</td></tr>';
	}
	}
	}
	}
	
	echo '</tbody></table>';
	$btn_go="Go";
	$btn_first="First";
	$btn_back="Back";
	$btn_next="Next";
	$btn_end="End";
	$total_recn="Total Record";
	if(!empty($this->set_text["go"])){
	$btn_go=$this->set_text["go"];
	}
	if(!empty($this->set_text["first"])){
	$btn_first=$this->set_text["first"];
	}
	if(!empty($this->set_text["back"])){
	$btn_back=$this->set_text["back"];
	}
	if(!empty($this->set_text["next"])){
	$btn_next=$this->set_text["next"];
	}
	if(!empty($this->set_text["end"])){
	$btn_end=$this->set_text["end"];
	}
	if(!empty($this->set_text["total_rec"])){
	$total_recn=$this->set_text["total_rec"];
	}
	echo '<div class="ktg_grid_footer_'.$this->ranKey.'">
	<input type="hidden"  value="'.$total_page.'"  id="max_page_'.$this->ranKey.'">
	<button type="button"  onclick="doSomething_'.$this->ranKey.'(\'cur_page_'.$this->ranKey.'\',\'first\')" class="ui-button">'.$btn_first.'</button>
	<button type="button"  id="back_page" '.$back_btn_false.' onclick="doSomething_'.$this->ranKey.'(\'cur_page_'.$this->ranKey.'\',\'back\')" class="ui-button">'.$btn_back.'</button>
	<input type="text" class="ktg_cur_page_'.$this->ranKey.'" value="'.$page.'" id="cur_page_'.$this->ranKey.'">
	/ <label>'.$total_page.'</label>
	<button type="button"   id="go"  onclick="doSomething_'.$this->ranKey.'(\'cur_page_'.$this->ranKey.'\',\'go\')" class="ui-button">'.$btn_go.'</button>
	<button type="button" id="next_page" '.$next_btn_false.' onclick="doSomething_'.$this->ranKey.'(\'cur_page_'.$this->ranKey.'\',\'next\')" class="ui-button">'.$btn_next.'</button>
	<button type="button" onclick="doSomething_'.$this->ranKey.'(\'cur_page_'.$this->ranKey.'\',\'end\')" class="ui-button">'.$btn_end.'</button>
	<label>'.$total_recn.' :'.$row_count.'</label>
	</div>';
        echo '</div>';
	
	}
	
	
	
	
	function __destruct()
	{
            @mysql_close($this->db);
            @mysql_free_result($this->sorgu);
	}
	
	
	}


	
	if (!empty($_POST['ktg_cont'])){
	$ranKey=$_GET['key'];	
	$dg = new ktg_grid($_SESSION['ktgg_query_'.$ranKey],$ranKey);
	
	if(!empty($_SESSION['ktgg_order_'.$ranKey])){
	$dg->set_order($_SESSION['ktgg_order_'.$ranKey]);
	}
	if(!empty($_SESSION['ktgg_row_limit_'.$ranKey])){
	$dg->set_row_limit($_SESSION['ktgg_row_limit_'.$ranKey]);
	}
	
	if(isset($_SESSION['ktgg_hidden_'.$ranKey])){
	foreach($_SESSION['ktgg_hidden_'.$ranKey] as $hiddens){
	$dg->set_col_hidden($hiddens);
	}
	}
	
	if(isset($_SESSION['ktgg_vir_col_key_'.$ranKey])){
	foreach($_SESSION['ktgg_vir_col_key_'.$ranKey] as $key){
	$dg->add_column($key);
	}
	}
	
	if (isset($_SESSION['ktgg_title_'.$ranKey])){
	foreach($_SESSION['ktgg_title_'.$ranKey] as $arg){
	$dg->set_col_title($arg);
	}
	}
	
	if(isset($_SESSION['ktgg_set_text_'.$ranKey])){
	foreach($_SESSION['ktgg_set_text_'.$ranKey] as $key=>$value){
	$dg->set_text($key,$value);
	}
	}
	
	if(isset($_SESSION['ktgg_set_js_'.$ranKey])){
	foreach($_SESSION['ktgg_set_js_'.$ranKey] as $key=>$value){
	$dg->set_js($key,$value);
	}
	}
	
	if(!empty($_SESSION['ktgg_num_show_'.$ranKey])){
	$dg->set_col_num_show($_SESSION['ktgg_num_show_'.$ranKey]);
	}
	
	if (!empty($_SESSION['ktgg_row_fix_'.$ranKey])){
	$dg->set_table_fix_hg($_SESSION['ktgg_row_fix_'.$ranKey]);
	}
	
	if(isset($_SESSION['ktgg_set_style_'.$ranKey])){
	foreach($_SESSION['ktgg_set_style_'.$ranKey] as $key=>$value){
	$dg->set_style($key,$value);
	}
	}
	
        if(isset($_SESSION['ktgg_set_field_'.$ranKey])){
        foreach ($_SESSION['ktgg_set_field_'.$ranKey] as $key=>$arr2){
            $dg->set_fields($key,$arr2[0]);
        }
        }
        
        if(isset($_SESSION['cond_field_'.$ranKey])){
        foreach ($_SESSION['cond_field_'.$ranKey] as $vals2){
        foreach ($vals2 as $key2=>$arr2){
        $dg->set_field_logic($arr2[3],$arr2[0],$arr2[1],$key2,$arr2[2]);
        }
        }
        }

        
	$dg->show();
	}


?>