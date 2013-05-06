<?php
    /**
        获取某段时间会话数的数据
        @zhaolianxiang in 2013-04-19
    **/

    header("cache-control:no-cache,must-revalidate");
    header("Content-Type:text/html;charset=utf-8");

    include_once('../DB/MyDB.class.php');

    //显示的列数
    if(isset($_GET['cols'])){
        $cols = $_GET['cols'];
    }else{
        $cols = 'c20';
    }

    //大类和小类
    $category = $_GET['category'];
    if($category == 0 || $category == 1001 || $category == 1002){
        $majorCategory = 0;
    }else{
        $majorCategory = $_GET['majorCategory'];
        if($majorCategory == -1){
            $majorCategory = 0;
        }
    }

    $where = " where postcategoryid=$category and postmajorcategoryid=$majorCategory ";

    //起止日期
    if(isset($_GET['date_start'])){
        $where .= "and analysisdate >= '{$_GET['date_start']}' ";
    }
    if(isset($_GET['date_end'])){
        $where .= "and analysisdate <= '{$_GET['date_end']}' ";
    }

    $db = new MyDB();
    $sql = "select analysisdate,c20,c10,c6,c5,c4,c3,c2,c1 from talkcount $where order by analysisdate asc";

    $firstRow = array();
    $tarr = array();
    $carr = array();

    $res = $db->query($sql);

    $firstRow = array('analysisdate');
    $colArr = explode(',',$cols);
    foreach($colArr as $value){
        $firstRow[] = $value;
    }

    $tarr[] = $firstRow;

    //对结果集进行处理，将结果封装在数组中
    while(($result = $db->fetch_assoc($res))){
        $total = 0;
        
	    //将$result转化成百分比的形式
	    foreach($result as $key => $value){
            if($key != "analysisdate"){
                $total += $result[$key];
            }
        }
        if($total > 0){
            foreach($result as $key => $value){
                if($key != "analysisdate"){
                    $result[$key] = round($result[$key] / $total, 4) * 100;
                }
            }
        }	
	
	    //table data，存储在顺序数组tarr中
        //chart data，存储在关联数组carr中
        $trow = array();
        $crow = array();

        array_push($trow, $result["analysisdate"]);
        $crow["analysisdate"] = $result["analysisdate"];
        
        foreach($colArr as $value){
            array_push($trow, $result[$value]);
            $crow[$value] = $result[$value];
        }

        $tarr[] = $trow;
        $carr[] = $crow;
    }

    $db->close();

    $arr = array(
        "tableData" => $tarr,
        "chartData" => $carr
    );

    //var_dump($arr);
    echo json_encode($arr);

?>
