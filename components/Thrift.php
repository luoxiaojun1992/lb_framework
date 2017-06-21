<?php

namespace lb\components;

use lb\BaseClass;
use lb\components\traits\Singleton;
use lb\Lb;
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

    public function with($service)
    {
        $thriftServicesConfig = Lb::app()->getThriftServicesConfig();
        $host = $thriftServicesConfig[$service]['gateway'];
        $endpoint = $thriftServicesConfig[$service]['endpoint'];

        $port = 80;
        if (strpos($host, ':')) {
            list($host, $port) = explode(':', $host);
        }

        $this->socket = new THttpClient($host, $port, $endpoint);
        $this->transport = new TBufferedTransport($this->socket, 1024, 1024);
        $this->protocol = new TBinaryProtocol($this->transport);

        $serviceArr = explode('\\', $service);
        $serviceName = ucfirst(array_pop($serviceArr));
        $clientName = '\\' . trim(implode('\\', array_merge($serviceArr, [$serviceName . 'Client'])), '\\');
        $this->client = new $clientName($this->protocol);

        return $this;
    }

    public function __call($name, $arguments)
    {
        $this->transport->open();

        $refecltionMethod = new \ReflectionMethod($this->client, $name);
        $res = $refecltionMethod->invokeArgs($this->client, $arguments);

        $this->transport->close();

        return $res;
    }
}
