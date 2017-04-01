<?php

namespace lb\controllers\console;

use \Swoole\Http\Server as HttpServer;

class SwooleController
{
    public function test()
    {
        $server = new HttpServer('127.0.0.1', 9501);

        $server->on('Request', function ($request, $response) {
            $response->end($request->server('request_uri'));
        });

        $server->start();
    }
}
