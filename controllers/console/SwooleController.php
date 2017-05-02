<?php

namespace lb\controllers\console;

use lb\applications\swoole\App;
use lb\components\helpers\JsonHelper;
use lb\components\jobs\SwooleTcpJob;
use lb\components\request\SwooleRequest;
use lb\components\response\SwooleResponse;
use lb\components\utils\IdGenerator;
use lb\Lb;
use \Swoole\Http\Server as HttpServer;
use \Swoole\Server as TcpServer;
use Swoole\Client as TcpClient;

class SwooleController extends ConsoleController
{
    protected $swooleConfig;

    protected function beforeAction()
    {
        $this->swooleConfig = Lb::app()->getSwooleConfig();

        parent::beforeAction();
    }

    /**
     * Swoole Http Server
     */
    public function http()
    {
        $this->writeln('Starting swoole http server...');

        $server = new HttpServer(
            $this->swooleConfig['http']['host'] ?? '127.0.0.1',
            $this->swooleConfig['http']['port'] ?? '9501'
        );

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

    /**
     * Swoole Tcp Server
     */
    public function tcp()
    {
        $this->writeln('Starting swoole tcp server...');

        $server = new TcpServer(
            $this->swooleConfig['tcp']['host'] ?? '127.0.0.1',
            $this->swooleConfig['tcp']['port'] ?? '9501'
        );

        $server->on('connect', function ($serv, $fd){
            $this->writeln('Client:Connect.');
        });

        $server->on('receive', function ($serv, $fd, $from_id, $data) {
            $jsonData = JsonHelper::decode($data);
            if (isset($jsonData['handler'])) {
                $handlerClass = $jsonData['handler'];
                if (class_exists('\Throwable')) {
                    try {
                        $serv->send(
                            $fd,
                            call_user_func_array([new $handlerClass, 'hanlder'],
                                ['data' => $data, 'fromId' => $from_id])
                        );
                    } catch (\Throwable $e) {
                        $serv->send($fd, 'Exception:' . $e->getTraceAsString());
                    }
                } else {
                    try {
                        $serv->send(
                            $fd,
                            call_user_func_array([new $handlerClass, 'hanlder'],
                                ['data' => $data, 'fromId' => $from_id])
                        );
                    } catch (\Exception $e) {
                        $serv->send($fd, 'Exception:' . $e->getTraceAsString());
                    }
                }
            }
        });

        $server->on('close', function ($serv, $fd) {
            $this->writeln('Client: Close.');
        });

        $server->start();
    }

    /**
     * Swoole Tcp Client Demo
     */
    public function client()
    {
        $client = new TcpClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $client->on('connect', function($cli) {
            $cli->send(JsonHelper::encode(['handler' => SwooleTcpJob::class]));
        });
        $client->on('receive', function($cli, $data){
            $this->writeln('Received: '.$data);
        });
        $client->on('error', function($cli){
            $this->writeln('Connect failed');
        });
        $client->on("close", function($cli){
            $this->writeln('Connection close');
        });

        $client->connect(
            $this->swooleConfig['tcp']['host'] ?? '127.0.0.1',
            $this->swooleConfig['tcp']['port'] ?? '9501',
            $this->swooleConfig['tcp']['timeout'] ?? 0.5
        );
    }
}
