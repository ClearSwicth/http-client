<?php
/**
 *
 * User: daikai
 * Date: 2021/5/20
 */

namespace ClearSwitch\Http;


use ClearsWitch\DataConversion\DataConversion;
use ClearSwitch\Http\Ioc\MackIoc;
use ClearSwitch\Http\Transports\TransportsInterface;

class Request
{
    /**
     * 默认的是get方法
     * @var string
     */
    protected $_method = "get";

    /** 代理的地址
     * @var
     */
    protected $_proxyHost;

    /**
     *代理的端口
     * @var
     */
    protected $_proxyPort;
    /**
     * 请求的头部信息
     * @var array
     */
    protected $_header = [];

    /**
     * 最大请求时间
     * @var array
     */
    protected $_timeout = '10';

    /**
     * 请求地址
     * @var
     */
    protected $_url;

    /**
     * @var 访问的通道
     */
    public $_transports = 'curl';

    /**
     * 默认的数据类型是json
     * @var string
     */
    public $_contentType = "json";

    /**
     * 请求的数据
     * @var string
     */
    public $_contentData = "";
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
        list($status, $headers, $content, $response) = $this->prepare()->send($this);
        return new Response($status, $headers, $content, $response);
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
        $transports = static::BUILT_IN_TRANSPORTS[$this->_transports];
        return (new MackIoc())->make($transports);
    }

    /**
     * 设置头部信息
     * @param array $header
     * @return $this
     * @author clearSwitch
     */
    public function setHeaders(array $header)
    {
        $this->_header = $header;
        return $this;
    }

    /**
     * Date: 2021/5/24 上午11:02
     * @return array
     * @author clearSwitch
     */
    public function getHeader()
    {
        return $this->_header;
    }

    /**
     * 设置请求地址
     * @param $url
     * @return $this
     * @author clearSwitch
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * 获得请求信息
     * @return mixed
     * @author clearSwitch
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * 设置请求方法并转换成大写
     * @param $method
     * @return $this
     * @author clearSwitch
     */
    public function setMethod(String $method)
    {
        $this->_method = strtoupper($method);
        return $this;
    }

    /**
     *
     * @return string
     * @author clearSwitch
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     *设置最大连接时间
     * @param $time
     * @return $this
     * @author clearSwitch
     */
    public function setTimeout($time)
    {
        $this->_timeout = $time;
        return $this;
    }

    /**
     * @return array|string
     * @author clearSwitch
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }

    public function getBody()
    {

    }

    /**
     * 转换请求的数据
     * @param array $content
     * @param String $type
     * @author clearSwitch
     */
    public function setContent(array $content, String $type = null)
    {
        if (!$type) {
            $type = $this->_contentType;
        }
        $obj = new DataConversion();
        $this->_contentData = $obj->dataConversion($content, $type);
        return $this;
    }

    /**
     *
     * @return string
     * @author clearSwitch
     */
    public function getContent()
    {
        return $this->_contentData;
    }

    /**
     * 设置代理
     * @param string $host 地址
     * @param int $port 端口
     * @return Request
     * @author clearSwitch。
     */
    public function setProxy($host, $port = null)
    {
        $this->_proxyHost = $host;
        $this->_proxyPort = $port;
        return $this;
    }

    /**
     * 获取代理地址
     * @return string
     * @author clearSwitch。
     */
    public function getProxyHost()
    {
        return $this->_proxyHost;
    }

    /**
     * 获取代理端口
     * @return int
     * @author clearSwitch。
     */
    public function getProxyPort()
    {
        return $this->_proxyPort;
    }
}
