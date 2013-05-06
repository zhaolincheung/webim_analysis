<?php 
class MyDB{
  private $host;
  private $name;
  private $password;
  private $mpdb;
  private $utf;

  function __construct(){
    $this->host = "192.168.116.80:3306";
    $this->name = "dbim";
    $this->password = "YI90uplo182jqo_P";
    $this->mpdb = "webim";
    $this->utf = "UTF-8";
    $this->connect();
  }
 
  function connect(){
    $link = mysql_connect($this->host,$this->name,$this->password)or die($this->error());
    mysql_select_db($this->mpdb,$link)or die("没有此数据库：".$this->mpdb);
    mysql_query("SET NAMES '$this->utf'");
  }
  
  function query($sql,$type=""){
    if(!($query = mysql_query($sql))) $this->show("say：",$sql); 
    return $query;
  }
  
  function show($message="",$sql=""){
    if(!$sql) echo $message;
    else echo $message."<br>".$sql;
  }

  function affected_rows(){
    return mysql_affected_rows(); 
  }
	
  function result($query,$row){
    return mysql_result($query,$row); 
  }
  
  function num_rows($query){
    return mysql_num_rows($query); 
  }
  
  function num_fields($query){
    return mysql_num_fields($query); 
  }
	
  function free_result($query){
    return mysql_free_result($query); 
  }
	
  function insert_id(){
   return mysql_insert_id(); 
  }
	
  function fetch_row($query){
   return mysql_fetch_row($query); 
  }
  
  function fetch_assoc($query){
   return mysql_fetch_assoc($query);
  }

  function version(){
    return mysql_get_server_info(); 
  }
  
  function close(){
    return mysql_close(); 
  }
}
?>
