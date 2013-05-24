<?php
    header("cache-control:no-cache,must-revalidate");
    header("Content-Type:text/html;charset=utf-8");

    include_once('../DB/MyDB.class.php');

    //显示的版本
    if(isset($_GET['versions'])){
        $versions = $_GET['versions'];
    }

    //查询项目
    if(isset($_GET['col'])){
        $col = $_GET['col'];
    }else {
        $col = "useractive";
    }

    //将版本号加上引号
    $versionArr = explode(',',$versions);
    $str_versions = '';
    $count = 0;
    foreach($versionArr as $value){
        if($count != 0){
            $str_versions .= ",";
        }
        $v = "'" . $value . "'";
        $str_versions .= $v;
        $count ++;
    }
    $where = " where version in ($str_versions) ";

    //起止日期
    if(isset($_GET['date_start'])){
        $where .= "and analysisdate >= '{$_GET['date_start']}' ";
    }
    if(isset($_GET['date_end'])){
        $where .= "and analysisdate <= '{$_GET['date_end']}' ";
    }

    $db = new MyDB();
    $sql = "select analysisdate,version,$col from danalysis $where order by analysisdate asc";
    $res = $db->query($sql);

    //对结果集进行处理，将同一天的数据封装在一条记录中
    //记录格式如下：{"2013-04-12":{v1:xx,v2:xx,v3:xx}}
    $temp = array();
    $yesterday = null;
    while(($result = $db->fetch_row($res))){
        $thisday = $result[0];

        //每处理完一天的记录，作为一行数据放到数组中
        if($yesterday != null && $thisday != $yesterday){
            $temp[$yesterday] = $row;
        }

        $row[$result[1]] = $result[2];
        $yesterday = $thisday;
    }
    $temp[$yesterday] = $row;

    $firstRow = array();
    $tarr = array();
    $carr = array();

    $firstRow = array('analysisdate');
    foreach($versionArr as $value)
        $firstRow[] = $value;

    $tarr[] = $firstRow;

    //将临时数组temp中的记录，处理成表格和图表所需要的数据格式
    foreach($temp as $key => $value){
        $trow = array();
        array_push($trow,$key);
        
        $crow = array();
        $crow["analysisdate"] = $key;
        
        foreach($value as $k => $v){
            array_push($trow,$v);
            $crow[$k] = $v;
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
