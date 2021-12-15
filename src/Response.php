<?php
/**
 *
 * User: daikai
 * Date: 2021/5/24
 */

namespace ClearSwitch\Http;


use ClearsWitch\DataConversion\DataConversion;

class Response
{
    /**
     * @var mixed|string
     */
    protected $_header;

    /**
     * @var mixed|string
     */
    protected $_content;

    /**
     * @var mixed|string
     */
    protected $_statusCode;

    /**
     * @var
     */
    protected $_response;

    /**
     * Response constructor.
     * @param mixed ...$args
     */
    public function __construct(...$args)
    {
        $this->_statusCode = !empty($args['0']) ? $args[0] : '';
        $this->_header = !empty($args['1']) ? $args[1] : '';
        $this->_content = !empty($args['2']) ? $args[2] : '';
        $this->_response = !empty($args['3']) ? $args[3] : '';
    }

    /**
     * Date: 2021/12/15 上午11:13
     * @return mixed|string
     * @author clearSwitch
     */
    public function getResponse()
    {
        return $this->_response;
    }

    public function getHttpVersion()
    {
        $headersData = explode("\r\n", $this->_header);
        return explode(' ', $headersData[0])[0];
    }

    /**
     * 获得请求头
     * @return false|string[]
     * @author clearSwitch
     */
    public function getHeaders()
    {
        $headerData = array();
        if ($this->_header) {
            $headersData = explode("\r\n", $this->_header);
            foreach ($headersData as $key => $item) {
                $header = explode(':', $item);
                if (!empty($header[1])) {
                    if (isset($headerData[$header[0]])) {
                        if (!is_array($headerData[$header[0]])) {
                            $headerData[$header[0]] = [$headerData[$header[0]]];
                        }
                        $headerData[$header[0]][] = $header[1];
                    } else {
                        $headerData[$header[0]] = $header[1];
                    }
                }
            }
        }
        return $headerData;
    }

    /**
     * 获得原样的头部信息的输出
     * @author clearSwitch
     */
    public function getRawHeaders()
    {
        return explode("\r\n", $this->_header);
    }

    /**
     * 获得返回的状态
     * @return mixed|string
     * @author clearSwitch
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * 返回值解析为数组，解析不了就原样输出
     * @return mixed
     * @author clearSwitch
     */
    public function getBody()
    {
        $obj = new DataConversion();
        $responseData = $obj->dataConversion($this->_content, 'array');
        if (is_array($responseData)) {
            return $responseData;
        } else {
            return $this->_content;
        }
    }

    /**
     * 原样输出
     * @return mixed|string
     * @author clearSwitch
     */
    public function getRawContent()
    {
        return $this->_content;
    }

    /**
     * 获得Cookies
     * @return array
     * @author clearSwitch
     */
    public function getCookies()
    {
        $result = [];
        $headers = $this->getHeaders();
        if (isset($headers['Set-Cookie'])) {
            if ($cookies = $headers['Set-Cookie']) {
                if (!is_array($cookies)) {
                    $cookies = [$cookies];
                }
                foreach ($cookies as $cookie) {
                    $cookie = $this->parseCookie($cookie);
                    $result[$cookie['key']] = $cookie;
                }
            }
        }
        return $result;
    }
}
