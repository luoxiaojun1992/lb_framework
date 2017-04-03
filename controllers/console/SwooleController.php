<?php

namespace lb\controllers\console;

use lb\Lb;
use \Swoole\Http\Server as HttpServer;

class SwooleController
{
    public function test()
    {
        $server = new HttpServer('127.0.0.1', 9501);

        $server->on('Request', function ($request, $response) {

            $this->setRequestParams($request);

            Lb::app()->setRouteInfo(true);

            Lb::app()->initWebApp();

            $response->end(Lb::app()->getHttpResponse());

        });

        $server->start();
    }

    protected function setRequestParams($request)
    {
        $_GET = $request->get;
        $_POST = $request->post;

        foreach ($request->get as $item => $value) {
            $_REQUEST[$item] = $value;
        }

        foreach ($request->post as $item => $value) {
            $_REQUEST[$item] = $value;
        }

        $_COOKIE = $request->cookie;

        foreach ($request->server as $item => $value) {
            $_SERVER[strtoupper($item)] = $value;
        }

        foreach ($request->header as $item => $value) {
            $_SERVER[strtoupper('HTTP_' . $item)] = $value;
        }

        foreach ($request->files as $item => $value) {
            $_FILES[$item] = $value;
        }

        $GLOBALS['HTTP_RAW_POST_DATA'] = $request->rawContent();
    }
}
