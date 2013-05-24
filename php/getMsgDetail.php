<?php
    header("cache-control:no-cache,must-revalidate");
    header("Content-Type:text/html;charset=utf8");
    
    include_once('../DB/MyDB.class.php');
    
    $talkid = $_GET['talkid'];
    if(!$talkid)
        return false;
    $fromuserid = $_GET['fromuserid'];
    $touserid = $_GET['touserid'];
    $postid = $_GET['postid'];

    $sql = "select fromuserid, touserid, content, updatetime from allmsg where talkid=$talkid and ((fromuserid=$fromuserid and touserid=$touserid) or (touserid=$fromuserid and fromuserid=$touserid)) and postid=$postid";
    
    $db = new MyDB();
    $res = $db->query($sql);
    while(($result = $db->fetch_assoc($res))) { 
        $arr[] = $result;
    }
    $db->close();

    echo "<table border=1>";
    echo "<tr><td>时间</td><td>fromuserid</td><td>touserid</td><td>消息内容</td></tr>";
    foreach ($arr as $line) {
        echo "<tr>";
        $date = date('Y-m-d H:i:s', $line[updatetime]);
        echo "<td>" . $date . "</td>";
        echo "<td>" . $line["fromuserid"] . "</td>";
        echo "<td>" . $line["touserid"] . "</td>";
        echo "<td>" . $line["content"] ."</td>";
        echo "</tr>";
    }
    echo "</table>";
?>
