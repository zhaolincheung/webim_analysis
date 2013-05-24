<?php
    header("cache-control:no-cache,must-revalidate");
    header("Content-Type:text/html;charset=utf-8");

    include_once('../DB/MyDB.class.php');

    $pageId = $_GET["pageid"];
    $pageSize = $_GET["pagesize"];
    $startPage = ($pageId -1) * $pageSize;

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

    $sql = "select talkid,fromuserid,touserid,postid,postcityid,channel,count(*) as msgcount,content from allmsg where ";

    if($category == 0 || $category == 1001 || $category == 1002){
        $sql .= " channel = $category";
    }else{
        $sql .= " channel = 0 and postcategoryid=$category ";
    }

    if($major_category !== NULL && $major_category != -1){
        $sql .= " and postmajorcategoryid=$major_category ";
    }

    $sql .= " and updatetime >= $thisdate and updatetime < $nextdate group by talkid order by msgcount desc limit $startPage, $pageSize";
    $db = new MyDB();
    $res = $db->query($sql);
    while(($result = mysql_fetch_assoc($res))){
        $arr[] = $result;
    }

    $db->close();
    echo json_encode($arr);
?>
