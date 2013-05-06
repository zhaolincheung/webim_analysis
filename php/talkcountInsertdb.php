<?php
    define('CODE_BASE2','/server/www/code_base');
    require_once CODE_BASE . '/app/category/CategoryNamespace.class.php';
	
    //include_once('../DB/MyDB.class.php');
    include_once('/data/webim/analys/DB/MyDB.class.php');

    //定义大类的编号
    $cidArr = array(0,2,1,11,15,14,7,6,4,12,5,13,16,3,9,1001,1002);
    //$cidArr = array(2,1,11,15,14,7,6,4,12,5,13,16,3,9,1001,1002);

    $yesterday = date("Y-m-d",strtotime("-1 day"));   

    //遍历大类获取小类并入库
    foreach($cidArr as $cid){
        if($cid ==0 || $cid == 1001 || $cid == 1002){
            //$majorCategory = -1;
            $majorCategory = 0;
            insert_db($cid, $majorCategory, $yesterday); 
        }else{
            //统计大类下面所有的小类的总和
            insert_db($cid,0,$yesterday);            
            
            $category = CategoryNamespace::getCategoryByScriptIndex($cid);
            $majorCategory = CategoryNamespace::getChildByUrl($category['source_name']);

            //echo json_encode($majorCategory);
            foreach($majorCategory as $line){
                $majorCategory = $line['script_index']; 
                insert_db($cid, $majorCategory, $yesterday); 
            }
        }
    }

    //根据指定的大类，小类，和日期,计算该天的消息量并入库
    function insert_db($category, $majorCategory, $date){
        $dateArr = explode("-", $date);
        if(checkdate($dateArr[1], $dateArr[2], $dateArr[0]) == false){
            return false;
        }

        $thisdate = mktime(0, 0, 0, $dateArr[1], $dateArr[2], $dateArr[0]);
        $nextdate = $thisdate + 3600 * 24;

        $sql = "select talkid,count(*) as xxx from allmsg where ";

        if($category == 0 || $category == 1001 || $category == 1002){
            $sql .= " channel = $category";
        }else{
            $sql .= " channel = 0 and postcategoryid=$category ";
        }

        if($majorCategory !== NULL && $majorCategory != 0){
            $sql .= " and postmajorcategoryid=$majorCategory ";
        }

        $sql .= " and updatetime >= $thisdate and updatetime < $nextdate group by talkid order by msgcount desc";
        //echo $sql . '<br/>';
        $db = new MyDB();
        $res = $db->query($sql);
        
	//初始化关联数组
        $mc["c20"]=$mc["c10"]=$mc["c6"]=$mc["c5"]=$mc["c4"]=$mc["c3"]=$mc["c2"]=$mc["c1"]=0;
        //分段统计消息数对应的会话数
        while(($result = $db->fetch_assoc($res))) 
        {
          $msgcount = $result["msgcount"];
          if($msgcount > 20)
            $mc["c20"]++;
          else if($msgcount > 10 && $msgcount <= 20)
            $mc["c10"]++;
          else if($msgcount >= 6 && $msgcount <= 10)
            $mc["c6"]++;
          else if($msgcount == 5)
            $mc["c5"]++;
          else if($msgcount == 4)
            $mc["c4"]++;
          else if($msgcount == 3)
            $mc["c3"]++;
          else if($msgcount == 2)
            $mc["c2"]++;
          else if($msgcount == 1)
            $mc["c1"]++;
        }
        
        //echo json_encode($mc);
        
        //insert table 'talkcount'
        $sql_insert = "insert into xxx(analysisdate,postcategoryid,postmajorcategoryid,c20,c10,c6,c5,c4,c3,c2,c1) values('";
        $sql_insert .= $date . "',";
        $sql_insert .= $category . ",";
        $sql_insert .= $majorCategory . ",";
        $sql_insert .= $mc["c20"] . ",";
        $sql_insert .= $mc["c10"] . ",";
        $sql_insert .= $mc["c6"] . ",";
        $sql_insert .= $mc["c5"] . ",";
        $sql_insert .= $mc["c4"] . ",";
        $sql_insert .= $mc["c3"] . ",";
        $sql_insert .= $mc["c2"] . ",";
        $sql_insert .= $mc["c1"] . ")";
        
	//echo $sql_insert . "\n";
        
        $db->query($sql_insert);

        $db->close();
    }
?>

