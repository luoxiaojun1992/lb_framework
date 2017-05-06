<?php

namespace lb\controllers\console;

use lb\applications\swoole\App;
use lb\components\consts\Protocol;
use lb\components\helpers\JsonHelper;
use lb\components\jobs\SwooleTcpJob;
use lb\components\request\SwooleRequest;
use lb\components\response\SwooleResponse;
use lb\components\utils\IdGenerator;
use lb\Lb;
use \Swoole\Http\Server as HttpServer;
use \Swoole\Server as TcpServer;
use Swoole\Websocket\Server as WebsocketServer;
use Swoole\Client as TcpClient;
use WebSocket\Client;

class SwooleController extends ConsoleController implements Protocol
{
    const DEFAULT_SWOOLE_HOST = '127.0.0.1';
    const DEFAULT_SWOOLE_PORT = '9501';
    const DEFAULT_SWOOLE_TIMEOUT = 0.5;

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
            $this->swooleConfig['http']['host'] ?? self::DEFAULT_SWOOLE_HOST,
            $this->swooleConfig['http']['port'] ?? self::DEFAULT_SWOOLE_PORT
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
            $this->swooleConfig['tcp']['host'] ?? self::DEFAULT_SWOOLE_HOST,
            $this->swooleConfig['tcp']['port'] ?? self::DEFAULT_SWOOLE_PORT
        );

        //防止粘包
        $server->set([
            'open_eof_split' => true,
            'package_eof' => self::EOF,
        ]);

        $server->on('connect', function ($serv, $fd){
            $this->writeln('Client:Connect.');
        });

        $server->on('receive', function ($serv, $fd, $from_id, $data) {
            $jsonData = JsonHelper::decode(trim($data, self::EOF));
            if (isset($jsonData['handler'])) {
                $jsonData['swoole_from_id'] = $from_id;
                $handlerClass = $jsonData['handler'];
                if (class_exists('\Throwable')) {
                    try {
                        $serv->send(
                            $fd,
                            Lb::app()->dispatchJob($handlerClass, $jsonData)
                        );
                    } catch (\Throwable $e) {
                        $serv->send($fd, 'Exception:' . $e->getTraceAsString());
                    }
                } else {
                    try {
                        $serv->send(
                            $fd,
                            Lb::app()->dispatchJob($handlerClass, $jsonData)
                        );
                    } catch (\Exception $e) {
                        $serv->send($fd, 'Exception:' . $e->getTraceAsString());
                    }
                }
            } else {
                $serv->send($fd, 'Handler not exists');
            }
        });

        $server->on('close', function ($serv, $fd) {
            $this->writeln('Client: Close.');
        });

        $server->start();
    }

    /**
     * Swoole UDP Server
     */
    public function udp()
    {
        $this->writeln('Starting swoole udp server...');

        $udpServer = new TcpServer(
            $this->swooleConfig['upd']['host'] ?? self::DEFAULT_SWOOLE_HOST,
            $this->swooleConfig['upd']['port'] ?? self::DEFAULT_SWOOLE_PORT,
            SWOOLE_PROCESS,
            SWOOLE_SOCK_UDP
        );

        //防止粘包
        $udpServer->set([
            'open_eof_split' => true,
            'package_eof' => self::EOF,
        ]);

        $udpServer->on('Packet', function ($serv, $data, $clientInfo) {
            $clientAddress = $clientInfo['address'];
            $clientPort = $clientInfo['port'];
            $jsonData = JsonHelper::decode(trim($data, self::EOF));
            if (isset($jsonData['handler'])) {
                $jsonData['swoole_client_info'] = $clientInfo;
                $handlerClass = $jsonData['handler'];
                if (class_exists('\Throwable')) {
                    try {
                        $serv->sendto(
                            $clientAddress,
                            $clientPort,
                            Lb::app()->dispatchJob($handlerClass, $jsonData)
                        );
                    } catch (\Throwable $e) {
                        $serv->sendto(
                            $clientAddress,
                            $clientPort,
                            'Exception:' . $e->getTraceAsString()
                        );
                    }
                } else {
                    try {
                        $serv->sendto(
                            $clientAddress,
                            $clientPort,
                            Lb::app()->dispatchJob($handlerClass, $jsonData)
                        );
                    } catch (\Exception $e) {
                        $serv->sendto(
                            $clientAddress,
                            $clientPort,
                            'Exception:' . $e->getTraceAsString()
                        );
                    }
                }
            } else {
                $serv->sendto(
                    $clientAddress,
                    $clientPort,
                    'Handler not exists'
                );
            }
        });

        $udpServer->start();
    }

    /**
     * Swoole Websocket Server
     */
    public function websocket()
    {
        $this->writeln('Starting swoole websocket server...');

        $ws = new WebsocketServer(
            $this->swooleConfig['ws']['host'] ?? self::DEFAULT_SWOOLE_HOST,
            $this->swooleConfig['ws']['port'] ?? self::DEFAULT_SWOOLE_PORT
        );

        $ws->on('open', function ($ws, $request) {
            $this->writeln('client-Connect.');
        });

        $ws->on('message', function ($ws, $frame) {
            $jsonData = JsonHelper::decode($frame->data);
            if (isset($jsonData['handler'])) {
                $jsonData['swoole_frame'] = $frame;
                $handlerClass = $jsonData['handler'];
                if (class_exists('\Throwable')) {
                    try {
                        $ws->push(
                            $frame->fd,
                            Lb::app()->dispatchJob($handlerClass, $jsonData)
                        );
                    } catch (\Throwable $e) {
                        $ws->push(
                            $frame->fd,
                            'Exception:' . $e->getTraceAsString()
                        );
                    }
                } else {
                    try {
                        $ws->push(
                            $frame->fd,
                            Lb::app()->dispatchJob($handlerClass, $jsonData)
                        );
                    } catch (\Exception $e) {
                        $ws->push(
                            $frame->fd,
                            'Exception:' . $e->getTraceAsString()
                        );
                    }
                }
            } else {
                $ws->push(
                    $frame->fd,
                    'Handler not exists'
                );
            }
        });

        $ws->on('close', function ($ws, $fd) {
            $this->writeln('client-closed');
        });

        $ws->start();
    }

    /**
     * Swoole Tcp Client Demo
     */
    public function tcpClient()
    {
        $this->writeln('Starting demo swoole tcp client...');

        $client = new TcpClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $client->on('connect', function($cli) {
            $cli->send(JsonHelper::encode(['handler' => SwooleTcpJob::class]) . self::EOF);
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
            $this->swooleConfig['tcp']['host'] ?? self::DEFAULT_SWOOLE_HOST,
            $this->swooleConfig['tcp']['port'] ?? self::DEFAULT_SWOOLE_PORT,
            $this->swooleConfig['tcp']['timeout'] ?? self::DEFAULT_SWOOLE_TIMEOUT
        );
    }

    /**
     * Swoole UDP Client Demo
     */
    public function udpClient()
    {
        $this->writeln('Starting demo swoole udp client...');

        $client = new TcpClient(SWOOLE_SOCK_UDP, SWOOLE_SOCK_ASYNC);

        $client->on('connect', function($cli) {
            $cli->send(JsonHelper::encode(['handler' => SwooleTcpJob::class]) . self::EOF);
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
            $this->swooleConfig['udp']['host'] ?? self::DEFAULT_SWOOLE_HOST,
            $this->swooleConfig['udp']['port'] ?? self::DEFAULT_SWOOLE_PORT,
            $this->swooleConfig['udp']['timeout'] ?? self::DEFAULT_SWOOLE_TIMEOUT
        );
    }

    /**
     * Websocket Client Demo
     */
    public function websocketClient()
    {
        $this->writeln('Starting demo websocket client...');

        $client = new Client(
            'ws://' .
            ($this->swooleConfig['ws']['host'] ?? self::DEFAULT_SWOOLE_HOST) . ':' .
            ($this->swooleConfig['ws']['port'] ?? self::DEFAULT_SWOOLE_PORT)
        );
        $client->send(JsonHelper::encode(['handler' => SwooleTcpJob::class]));
        $this->writeln($client->receive());
        $client->close();
    }
}
