<?php
/**
 *
 * User: daikai
 * Date: 2021/5/20
 */

namespace ClearSwitch\Http\Transports;


use ClearSwitch\Http\BatchRequest;
use ClearSwitch\Http\Request;

interface TransportsInterface
{
    /**
     * 单个访问
     * @return mixed
     * @author clearSwitch
     */
    public function send(Request $request);

    /**
     * 批量访问
     * @return mixed
     * @author clearSwitch
     */
    public function batchSend($request);
    // public function batchSend(BatchRequest $request);
}
