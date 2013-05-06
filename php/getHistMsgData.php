<?php
    header("cache-control:no-cache,must-revalidate");
    header("Content-Type:text/html;charset=utf-8");

    include_once('../DB/MyDB.class.php');

    //显示的列数
    if(isset($_GET['cols'])){
      $cols = $_GET['cols'];
    }else{
      $cols = 'msgcount';
    }

    //周同比
    if(isset($_GET['week'])){
      $week = $_GET['week'];
    }

    //渠道
    if(isset($_GET['clienttype'])){
      $clienttype = $_GET['clienttype'];
    }
    else{
      $clienttype = 0;
    }

    $where = " where clienttype=$clienttype";

    if(isset($week)){
      $where .= " and dayofweek(analysisdate)=$week ";
    }

    //频道
    if(isset($_GET['channel'])){
      $channel = $_GET['channel'];
    }
    else{
      $channel = 0;
    }

    $where .= " and channel=$channel ";

    //起止日期
    if(isset($_GET['date_start'])){
      $where .= "and analysisdate >= '{$_GET['date_start']}' ";
    }
    if(isset($_GET['date_end'])){
      $where .= "and analysisdate <= '{$_GET['date_end']}' ";
    }

    $colArr = explode(',',$cols);
    
    $flag = FALSE;
    //当查询详情页pv或uv时
    if(strstr($cols, "detail")){
        $flag = TRUE;
        
        $sql = "select analysisdate,";     
        //查询字段不包含msgcount
        if($colArr[0] != "msgcount"){
            $sql .= "msgcount,";
        }
        //查询字段不包含usercount
        if(strstr($cols, "usercount") == FALSE){
            $sql .= "usercount,";
        }
        $sql .= "$cols from xxx $where order by analysisdate asc";
    }else{
        $sql = "select analysisdate,$cols from xxx $where order by analysisdate asc";
    }

    $firstRow = array();
    $tarr = array();
    $carr = array();
    
    $db = new MyDB();
    $res = $db->query($sql);

    //添加第一行
    $firstRow = array('analysisdate');
    foreach($colArr as $value){
      $firstRow[] = $value;
    }
    $tarr[] = $firstRow;

    //对结果集进行处理，将结果封装在数组中
    while(($result = $db->fetch_assoc($res))){
        //将pv和uv转换成百分比 
        if(strstr($cols, "detailpv") && $result["detailpv"] != 0){
            $result["detailpv"] = round($result["msgcount"] / $result["detailpv"] * 100, 4);
        }
        if(strstr($cols, "detailuv") && $result["detailuv"] != 0){
            $result["detailuv"] = round($result["usercount"] / $result["detailuv"] * 100, 4);
        }

        $trow = array();
        $crow = array();

	    array_push($trow, $result["analysisdate"]);

        if($flag == TRUE){
            $crow["analysisdate"] = $result["analysisdate"];

            foreach($colArr as $value){
                array_push($trow, $result[$value]);
                $crow[$value] = $result[$value];
            }            
        }else{
            foreach($colArr as $value){
                array_push($trow, $result[$value]);
            }
            
            $crow = $result;
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
