<?php
    header("cache-control:no-cache,must-revalidate");
    header("Content-Type:text/html;charset=utf-8");

    include_once('../DB/MyDB.class.php');

    //转换文本
    function revert($key){
        switch($key){
	        case 'c1':
	            $k = '1';
	            break;
	        case 'c2':
	            $k = '2';
	            break;
	        case 'c3':
	            $k = '3';
	            break;
	        case 'c4':
	            $k = '4';
	            break;
	        case 'c5':
	            $k = '5';
	            break;
	        case 'c6':
	            $k = '6-10';
	            break;
	        case 'c10':
	            $k = '11-20';
	            break;
	        case 'c20':
	            $k = '20+';
	            break;
	        default:
	            $k = '0';    
        }
        return $k;
    }

    $category = $_GET['category'];
    if($category == 0 || $category == 1001 || $category == 1002){
        $major_category = 0;
    }else{
        $major_category = $_GET['majorCategory'];
        if($major_category == -1){
            $major_category = 0;
        }
    }

    $date = $_GET['dt'];

    $where = "where postcategoryid=$category and postmajorcategoryid=$major_category and analysisdate='$date'";

    $sql = "select c20,c10,c6,c5,c4,c3,c2,c1 from talkcount $where order by analysisdate asc";

    $db = new MyDB();
    $res = $db->query($sql);

    //获取相应的分段统计消息数
    while(($result = $db->fetch_assoc($res))){
        $mc = $result;
    }

    foreach($mc as $key => $value){
        $k = revert($key);
        $row["talkratio"] = $k;
        $row["value"] = $value;
        $carr[] = $row;
    }

    $arr = array(
        "tableData" => $mc,
        "chartData" => $carr
    );

    $db->close();
    echo json_encode($arr);
?>
