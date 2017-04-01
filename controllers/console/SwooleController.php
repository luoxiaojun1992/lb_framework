<?php

namespace lb\controllers\console;

class SwooleController
{
    public function test()
    {
        $server = new Swoole\Http\Server('127.0.0.1', 9501);

        $server->on('Request', function ($request, $response) {
            $response->end(" swoole response is ok");
        });

        $server->start();
    }
}
