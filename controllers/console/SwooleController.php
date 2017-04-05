<?php

namespace lb\controllers\console;

use lb\applications\swoole\App;
use lb\components\request\SwooleRequest;
use lb\components\response\SwooleResponse;
use lb\components\utils\IdGenerator;
use \Swoole\Http\Server as HttpServer;

class SwooleController
{
    public function test()
    {
        $server = new HttpServer('127.0.0.1', 9501);

        $server->on('Request', function ($request, $response) {

            $swooleRequest = (new SwooleRequest())->setSwooleRequest($request);
            $swooleResponse = (new SwooleResponse())->setSwooleResponse($response);
            $sessionId = $swooleRequest->getCookie('swoole_session_id');
            if (!$sessionId) {
                $sessionId = IdGenerator::component()->generate();
                $swooleResponse->setCookie('swoole_session_id', $sessionId);
            }
            $swooleRequest->setSessionId($sessionId);
            $swooleResponse->setSessionId($sessionId);

            (new App($swooleRequest, $swooleResponse))->run();

        });

        $server->start();
    }
}
