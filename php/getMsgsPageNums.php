<?php
	include_once('../DB/MyDB.class.php');
    $pageSize = $_GET["pagesize"];
    
    $category = $_GET['category'];

    if($category == 0 || $category == 1001 || $category == 1002){
	$major_category = -1;
    }else{
	$major_category = $_GET['majorCategory'];
    }

    $date = $_GET['dt'];
    $dateArr = explode("-", $date);
    if(checkdate($dateArr[1], $dateArr[2], $dateArr[0]) == false){
        return false;
    }

    $thisdate = mktime(0, 0, 0, $dateArr[1], $dateArr[2], $dateArr[0]);
    $nextdate = $thisdate + 3600 * 24;

    $sql = "select talkid,count(*) as msgcount from allmsg where ";

    if($category == 0 || $category == 1001 || $category == 1002){
	$sql .= " channel = $category";
    }else{
	$sql .= " channel = 0 and postcategoryid=$category ";
    }

    if($major_category !== NULL && $major_category != -1){
	$sql .= " and postmajorcategoryid=$major_category ";
    }

    $sql .= " and updatetime >= $thisdate and updatetime < $nextdate group by talkid order by msgcount desc";
	
    $db = new MyDB();
    $res = $db->query($sql);
    $total = $db->num_rows($res);
    $pageNums = ceil($total / $pageSize);
    $db->close();
	
    echo $pageNums;		
?>
