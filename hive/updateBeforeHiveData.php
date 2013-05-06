<?php
    define('CODE_BASE2','/server/www/code_base');
    $GLOBALS['THRIFT_ROOT'] = !isset($GLOBALS['THRIFT_ROOT']) ? CODE_BASE2 . '/third_part/thrift-0.5.0' : $GLOBALS['THRIFT_ROOT'];
    
    require_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';
    require_once $GLOBALS['THRIFT_ROOT'].'/packages/scribe/scribe.php';
    require_once $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php';
    require_once $GLOBALS['THRIFT_ROOT'].'/transport/TSocket.php';
    require_once $GLOBALS['THRIFT_ROOT'].'/transport/THttpClient.php';
    require_once $GLOBALS['THRIFT_ROOT'].'/transport/TFramedTransport.php';
    require_once $GLOBALS['THRIFT_ROOT'].'/transport/TBufferedTransport.php';

    //生成的文件
    require_once dirname(__FILE__) . '/Hive.php';
    //数据库文件    
    include_once('../DB/MyDB.class.php');
    
    ERROR_REPORTING(E_ALL);
    INI_SET('DISPLAY_ERRORS','ON');

    $socket = new TSocket('hive.corp.abc.com',13080);
    $socket->setDebug(TRUE); 
    
    // 设置接收超时（毫秒）
    $socket->setSendTimeout(10000);
    $socket->setRecvTimeout(30*60000);//接受时间设置为30分钟
     
    //$transport = new TBufferedTransport($socket, 1024, 1024);
    $transport = new TFramedTransport($socket);
    $protocol = new TBinaryProtocol($transport);
    $client = new HiveClient($protocol);

    try{
        $transport->open();
    }catch(TException $tx){
        echo $tx->getMessage();
        exit;
    }
     
    $yesterday = $_SERVER["argv"][1];
    echo $yesterday . "\n";
    //exit;
    
    $sqlArr = array(
        //查看全站详情页的PV UV
        //"total" => "select count(*), count(distinct uuid) from xxx where dt = '$yesterday' and gjch regexp '[^@]*/detail';",
        //获取各个类目某一天的PV和UV
        "category" => "select regexp_extract(gjch, '^/([^/]+)', 1), count(*), count(distinct uuid) from xxx where dt = '$yesterday' and gjch regexp '[^@]*/detail' GROUP BY regexp_extract(gjch, '^/([^/]+)', 1);"    
    );

    foreach($sqlArr as $key => $value){
        echo $key .":" . $value ."\n";
     
        //想hadoop提交任务,如果失败重新提交 
        $num = 50; 
        while($num > 0){
	    try{
	        $taskId = $client->submitTask('xxx@xx.com','web',$value);
                echo "taskid:" . $taskId . "\n";
                if($taskId <= 0){
                    echo 'error submit';
                    sleep(60);
                }else{//提交成功,退出
                    break;
                }
            }catch(TException $tx){
                echo $tx->getMessage();
                sleep(60);
            }
            $num --;
        }
              
        //轮询任务是否完成 
        $count = 50;
        while($count > 0){
            try{
                //sleep以秒为单位,这里3分钟轮询一次
                sleep(3*60);
            }catch(TException $tx){}
            
            if($client->isTaskFinished($taskId)){
                $uri = $client->getResultURI($taskId);
                echo $uri . "\n";
                
                //判断fopen是否出错，如果出错再重新打开 
                for($i = 50; $i > 0; $i --){
	            try{
                        $handle = fopen($uri,"rb");
                        //获取文件内容，并去掉首尾的空白符
                        $content = trim(stream_get_contents($handle));
                        if($content){
                            break;
                        }
                        fclose($handle);
                    }catch(TeXception $tx){
                        echo $tx->getMessage();
                        sleep(60);
                    }
                }

                break;
            }
            $count--;
        }

        //作业执行完毕，对结果进行入库
        if($content){
            $db = new MyDB();

            if($key == 'total'){
                $valArr = explode("\t", $content);
                $sql = "update xxx set detailpv=$valArr[0],detailuv=$valArr[1] where analysisdate='$yesterday' and abc=0 and efg=0";
                echo $sql . "\n";

                $res = $db->query($sql);
            }else if($key == 'category'){
                $lineArr = explode("\n", $content);

                foreach($lineArr as $line){
                    $valArr = explode("\t", $line);
                    $channelId = getChannelId($valArr[0]);
                    
                    if(-1 != $channelId){
                        $sql = "update xxx set detailpv=$valArr[1],detailuv=$valArr[2] where analysisdate='$yesterday' and abc=$channelId and efg=0";
                        echo $sql . "\n";

                        $res = $db->query($sql);
                    }
                }
            }
            $db->close();
        }
    }
    $transport->close();

    //返回频道对应的id号
    function getChannelId($channel){
        switch($channel){
            //同城活动
            case "huodong":
                return 12;
            //宠物
            case "chongwu":
                return 1;
            //其他
            default:
                return -1;
        }
    }
?>
