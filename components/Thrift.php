<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\traits\Singleton;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\THttpClient;

class Thrift extends BaseClass
{
    use Singleton;

    protected $socket;
    /** @var  TBufferedTransport */
    protected $transport;
    protected $protocol;
    protected $client;

    private function __construct()
    {
        //
    }

    public function with($host, $endpoint, $service)
    {
        $port = 80;
        if (strpos($host, ':')) {
            list($host, $port) = explode(':', $host);
        }

        $serviceArr = explode('/', $service);
        $serviceName = ucfirst(array_pop($serviceArr));

        $this->socket = new THttpClient($host, $port, $endpoint);
        $this->transport = new TBufferedTransport($this->socket, 1024, 1024);
        $this->protocol = new TBinaryProtocol($this->transport);

        $clientName = '\\' . trim(implode('\\', array_merge($serviceArr, [$serviceName . 'Client'])), '\\');
        $this->client = new $clientName($this->protocol);
    }

    public function __call($name, $arguments)
    {
        $this->transport->open();

        call_user_func_array([$this->client, $name], $arguments);

        $this->transport->close();
    }
}
