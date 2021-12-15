<?php
/**
 *
 * User: daikai
 * Date: 2021/5/20
 */

namespace ClearSwitch\Http\Transports;


use ClearSwitch\Http\BatchRequest;
use ClearSwitch\Http\Request;

class CurlTransports implements TransportsInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * 单个请求
     * @param Request $request
     * @return array|mixed
     * @throws \Exception
     * @author clearSwitch
     */
    public function send(Request $request)
    {
        $this->request = $request;
        $ch = curl_init();
        curl_setopt_array($ch, $this->prepare());
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch) ?: curl_strerror(curl_errno($ch));
            curl_close($ch);
            throw new \Exception($error);
        }
        $requestInfo = curl_getinfo($ch);
        $status = $requestInfo['http_code'];
        $headerSize = $requestInfo['header_size'];
        $headers = mb_substr($response, 0, $headerSize - 4);
        $content = mb_substr($response, $headerSize);
        $headers = explode("\r\n", $headers);
        $headers = implode("\r\n", $headers);
        curl_close($ch);
        return [$status, $headers, $content, $response];
    }

    /**
     * 批量的请求
     * @param BatchRequest $request
     * @return mixed
     * @author clearSwitch
     */
    public function batchSend($request)
    {
        $mh = curl_multi_init();
        foreach ($request as $v) {
            $this->request = $v;
            $conn = curl_init();
            curl_setopt_array($conn, $this->prepare());
            $requests[] = $conn;
            curl_multi_add_handle($mh, $conn);
        }
        do {
            curl_multi_exec($mh, $active);
        } while ($active);
        //4、获取结果
        foreach ($requests as $i => $value) {
            $response = curl_multi_getcontent($value);
            $requestInfo = curl_getinfo($value);
            $status = $requestInfo['http_code'];
            $headerSize = $requestInfo['header_size'];
            $headers = mb_substr($response, 0, $headerSize - 4);
            $content = mb_substr($response, $headerSize);
            $headers = explode("\r\n", $headers);
            $headers = implode("\r\n", $headers);
            $responses[$i] = [$status, $headers, $content, $response];
        }
        //5、移除子handle，并close子handle
        foreach ($requests as $i => $value) {
            curl_multi_remove_handle($mh, $value);
            curl_close($value);
        }
        //6、关闭批处理handle
        curl_multi_close($mh);
        return $responses;

    }

    /**
     * curl 的准备工作
     * @return array
     * @author clearSwitch
     */
    protected function prepare()
    {
        if ($this->request->getMethod() == 'GET' && is_array($this->request->getContent())) {
            $url = $this->request->getUrl() . "?" . http_build_query($this->request->getContent());
        } else {
            $url = $this->request->getUrl();
        }
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $this->request->getMethod(),
            CURLOPT_SSL_VERIFYPEER => false,
            //设定是否显示头信息
            CURLOPT_RETURNTRANSFER => true,
            //设置这个选项为一个非零值(象 “Location: “)的头，服务器会把它当做HTTP头的一部分发送(注意这是递归的，PHP将发送形如 “Location: “的头)。
            CURLOPT_FOLLOWLOCATION => 1,
            //自动设置header中的referer信息
            CURLOPT_AUTOREFERER => 1,
            //如果你想把一个头包含在输出中，设置这个选项为一个非零值。
            CURLOPT_HEADER => 1
        ];
        if ($this->request->getMethod() === 'HEAD') {
            //如果你不想在输出中包含body部分，设置这个选项为一个非零值
            $options[CURLOPT_NOBODY] = true;
            unset($options[CURLOPT_WRITEFUNCTION]);
        }
        if (!in_array($this->request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            //传递一个作为HTTP “POST”操作的所有数据的字符串。
            unset($options[CURLOPT_POSTFIELDS]);
            //如果你想PHP去做一个正规的HTTP POST，设置这个选项为一个非零值。这个POST是普通的 application/x-www-from-urlencoded 类型，多数被HTML表单使用。
            unset($options[CURLOPT_POST]);
        }
        if ($this->request->getHeader()) {
            foreach ($this->request->getHeader() as $k => $item) {
                $options[CURLOPT_HTTPHEADER][] = $k . ':' . $item;
            }
        }
        if (!empty($this->request->getContent())) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $this->request->getContent();
        }
        //设置代理的地址
        if ($this->request->getProxyHost()) {
            $options[CURLOPT_PROXY] = $this->request->getProxyHost();
        }
        //设置代理的端口
        if ($this->request->getProxyPort()) {
            $options[CURLOPT_PROXYPORT] = $this->request->getProxyPort();
        }
        //设置一个长整形数，作为最大延续多少秒。
        $options[CURLOPT_TIMEOUT] = $this->request->getTimeout();
        //在发起连接前等待的时间，如果设置为0，则不等待。
        $options[CURLOPT_CONNECTTIMEOUT] = $this->request->getTimeout();
        return $options;
    }
}
