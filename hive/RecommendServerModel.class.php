<?php
/**
 * @package              
 * @subpackage           
 * @author               $Author:   yangyu$
 * @file                 $HeadURL: http://svn.ganji.com:8888/svn/ganji_v3/trunk/apps/housing/premier/common/cpc/CpcServerModel.class.php $
 * @version              $Rev: 84828 $
 * @lastChangeBy         $LastChangedBy: luorui $
 * @lastmodified         $LastChangedDate: 2011-12-14 18:48:55 +0800 (三, 2011-12-14) $
 * @copyright            Copyright (c) 2010, www.ganji.com
 */
$GLOBALS['THRIFT_ROOT'] = !isset($GLOBALS['THRIFT_ROOT']) ? CODE_BASE2 . '/third_part/thrift-0.5.0' : $GLOBALS['THRIFT_ROOT'];
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';
require_once $GLOBALS['THRIFT_ROOT'].'/packages/scribe/scribe.php';
require_once $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/THttpClient.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TFramedTransport.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TBufferedTransport.php';
require_once dirname(__FILE__) . '/RelatedXiaoquRecommend.php';
require_once dirname(__FILE__) . '/related_xiaoqu_recommend_types.php';
require_once dirname(__FILE__) . '/RelatedRecommend.php';
require_once dirname(__FILE__) . '/related_recommend_types.php';
require_once GANJI_CONF .'/RecommendConfig.class.php';

class RecommendServerModel{
    protected static $adProtocol   = null;
    protected static $adTransport  = null;
    protected static $housingProtocol   = null;
    protected static $housingTransport  = null;

    /* {{{ static getRecommendXiaoquServerModel */
    /**
     * @brief 获得AD广告server的资源
     *
     * @returns  TBinaryProtocol 
     * @exception
     */
    public static function getRecommendXiaoquServerModel($debug = false){
        try{
            $msg = '';
            $len = count(RecommendConfig::$HousingRecommendXiaoquServerInfo);
            for($i=0;$i < $len;$i++){
                $socket = new TSocket(RecommendConfig::$HousingRecommendXiaoquServerInfo[$i]['host'], RecommendConfig::$HousingRecommendXiaoquServerInfo[$i]['port']);
                //            $socket->setDebug($debug);
                //           $socket->open();
                // 设置接收超时（毫秒）
                $socket->setSendTimeout(100);
                $socket->setRecvTimeout(100);

                self::$adTransport  = new TBufferedTransport($socket);//, 1024, 1024);
                self::$adProtocol   = new TBinaryProtocol(self::$adTransport);
                $client = new RelatedXiaoquRecommendClient(self::$adProtocol, self::$adProtocol);
                try{
                    self::$adTransport->open();
                    return $client;
                }
                catch(TException $tx){
                    $msg .= $tx->getMessage();
                }
            }
            //所有地址都连接不上抛出错误
            throw new Exception($msg);
        }
        catch(TException $tx) {
            throw new Exception($tx->getMessage());
        }
    }//}}}
    /* {{{ static getRecommendHousingServerModel */
    /**
     * @brief 获得AD广告server的资源
     *
     * @returns  TBinaryProtocol 
     * @exception
     */
    public static function getRecommendHousingServerModel($debug = false){
        try{
            $msg = '';
            $len = count(RecommendConfig::$HousingRecommendSimilarServerInfo);
            for($i=0;$i < $len;$i++){
                $socket = new TSocket(RecommendConfig::$HousingRecommendSimilarServerInfo[$i]['host'], RecommendConfig::$HousingRecommendSimilarServerInfo[$i]['port']);
                //            $socket->setDebug($debug);
                //           $socket->open();
                // 设置接收超时（毫秒）
                $socket->setSendTimeout(100);
                $socket->setRecvTimeout(100);

                self::$housingTransport  = new TBufferedTransport($socket);//, 1024, 1024);
                self::$housingProtocol   = new TBinaryProtocol(self::$housingTransport);
                $client = new RelatedRecommendClient(self::$housingProtocol, self::$housingProtocol);
                try{
                    self::$housingTransport->open();
                    return $client;
                }
                catch(TException $tx){
                    $msg .= $tx->getMessage();
                }
            }
            //所有地址都连接不上抛出错误
            throw new Exception($msg);
        }
        catch(TException $tx) {
            throw new Exception($tx->getMessage());
        }
    }//}}}
    /* {{{ static getRecommendSimilarHousingListServerModel */
    /**
     * @brief 获得列表页补充周边房源服务资源
     *
     * @returns  TBinaryProtocol 
     * @exception
     */
    public static function getRecommendSimilarHousingListServerModel($debug = false){
        try{
            $msg = '';
            $len = count(RecommendConfig::$HousingListRecommendSimilarServerInfo);
            for($i=0;$i < $len;$i++){
                $socket = new TSocket(RecommendConfig::$HousingListRecommendSimilarServerInfo[$i]['host'], RecommendConfig::$HousingListRecommendSimilarServerInfo[$i]['port']);
                // 设置接收超时（毫秒）
                $socket->setSendTimeout(100);
                $socket->setRecvTimeout(100);

                self::$housingTransport  = new TBufferedTransport($socket);//, 1024, 1024);
                self::$housingProtocol   = new TBinaryProtocol(self::$housingTransport);
                $client = new FangListFillRecommendClient(self::$housingProtocol, self::$housingProtocol);
                try{
                    self::$housingTransport->open();
                    return $client;
                }
                catch(TException $tx){
                    $msg .= $tx->getMessage();
                }
            }
            //所有地址都连接不上抛出错误
            throw new Exception($msg);
        }
        catch(TException $tx) {
            throw new Exception($tx->getMessage());
        }
    }//}}}
    /* {{{ __destruct */
    /**
     * @brief __destruct
     *
     * @returns   
     */
    public function __destruct(){
        try{
            self::$adTransport->close();
            self::$housingTransport->close();
        }
        catch(TException $tx){
            Logger::logError($tx->getMessage() . "\n". $tx->getTraceAsString() , 'recommend');
        }
    }//}}}
}
