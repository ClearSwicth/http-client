```php
use ClearSwitch\Http\Request;
/**
 * 构建器配置 (可选)
 * 有三个json ,array,xml,类型
 * 通过选择构造器来设置请求参数的
 */
$request->setContent(['data'=>['waybill=>1','age'=>"3"]],'json');

/**
 * 解析器配置 (可选)
 * 返回的response 自动解析为数组
 * 如果返回的response 自带的解析器（json,xml,array）不能够解析就原样输出
 */

/**
 * 目前只支持curl
 */

## 设置请求参数
```php
$request->setUrl($url); //设置请求URL
$request->setMethod($method); //设置请求方法
$request->setHeaders([$name => $value, ...]); //设置请求头部
$request->setContent($data, $serializer = null);//请求的数据
$request->setBody([$name => $value, ...]); //设置消息体参数
$request->setTimeout($timeout); //设置超时时间

## 响应
$response->getRawResponse(); //获取响应原文
$response->getRawContent(); //获取消息体原文
$response->getRawHeaders(); //获取头部原文
$response->getBody(); //获取解析后的消息体参数
$response->getHeaders(); //获取解析后的头部
$response->getStatusCode(); //获取状态码
$response->getContentType(); //获取消息体类型
$response->getHttpVersion(); //获取HTTP版本
```
## 设置请求参数
```php
use ClearSwitch\Http\BatchRequest;

$requests = [];

for($i = 0; $i < 100; $i++){
    $request = new Request();
    $request->setUrl($url);
    $request->addQuery('id', $i);
    $requests[] = $request;
}

$batch->setRequests($requests);

/**
 * 返回内容为数组，keyValue对应关系与构造BatchRequest时传入的数组相同
 * 遍历返回的结果，结果与Request调用send方法后返回的内容一致，使用方法也相同
 */
$response = $batch->send();
/**
 * 传输组件配置
 * 内置了三种传输组件，分别是：
 *   cUrl, 基于cUrl的传输组件
 *   coroutine 基于Swoole的协程的传输组件
 *   stream 基于Streams的传输组件
 * 可自行新增或覆盖相应的传输组件
 */

