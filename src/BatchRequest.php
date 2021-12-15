<?php
/**
 *
 * User: daikai
 * Date: 2021/5/24
 */

namespace ClearSwitch\Http;

use ClearSwitch\Http\Transports\TransportsInterface;

/**
 * 批量请求
 * Class BatchRequest
 * @package ClearSwitch\Http
 */
class BatchRequest
{

    /**
     * 默认的批量一次是50
     * @var int
     */
    protected $_batchNumber=2;

    /**
     * @var array
     */
    protected $_requests=[];
    /**
     * @var 访问的通道
     */
    public $_transports = 'curl';
    /**
     * http 连接器
     */
    const BUILT_IN_TRANSPORTS = [
        'curl' => 'ClearSwitch\Http\Transports\CurlTransports'
    ];

    /**
     * Date: 2021/5/20 下午4:45
     * @throws \Exception
     * @author clearSwitch
     */
    public function send()
    {
        $result=[];
        if(empty($this->_requests)){
            throw new \Exception('请求体不能为空:');
        }
        foreach ($this->getRequests() as $v){
            $response=$this->prepare()->batchSend($v);
            $result=array_merge($result,$response);
        };
       // $response=$this->prepare()->batchSend($this);
        foreach ($result as $v){
            $responses[]= new Response(... $v);
        }
        return $responses;
    }

    /**
     * 设置批量请求
     * @param $request
     * @return $this
     * @author clearSwitch
     */
    public function setRequests($request,$batchNumber=null){
        if($batchNumber){
            $this->_batchNumber=$batchNumber;
        }
        $this->_requests=array_chunk($request,$this->_batchNumber);
        return $this;
    }

    /**
     * 获得请求的内容
     * @return array
     * @author clearSwitch
     */
    public function getRequests(){
        return $this->_requests;
    }

    /**
     * Date: 2021/5/20 下午4:46
     * @return TransportsInterface
     * @throws \Exception
     * @author clearSwitch
     */
    protected function prepare()
    {
        if (in_array($this->_transports, static::BUILT_IN_TRANSPORTS)) {
            throw new \Exception('Unkrown transport:' . $this->_transports);
        }
        $transports=static::BUILT_IN_TRANSPORTS[$this->_transports];
        return new $transports();
    }

    /**
     * 设置访问通道
     * @param $transports
     * @return $this
     * @author clearSwitch
     */
    public function setTransports($transports){
        $this->_transports=$transports;
        return $this;
    }

}
