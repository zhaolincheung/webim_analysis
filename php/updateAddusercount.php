<?php

    include_once('/data/webim/analys/DB/MyDB.class.php');
    
    $yesterday = date("Y-m-d",strtotime("-1 day"));
    $today = date("Y-m-d");

    $sql = "select version,count(*) as addusercount from xxx where create_time>unix_timestamp('$yesterday') and create_time<unix_timestamp('$today') group by version";   
    
    $db = new MyDB();
    $res = $db->query($sql);

    while(($result=$db->fetch_assoc($res))){
        $version = $result["version"];
        $addusercount = $result["addusercount"];
        $sql_update = "update xxx set addusercount=$addusercount where analysisdate='$yesterday' and version='$version'";
    	
        //echo $sql_update . "\n";
        
        $db->query($sql_update);
    }
    $db->close();
?>
