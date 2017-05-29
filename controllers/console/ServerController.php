<?php

namespace lb\controllers\console;

use lb\Lb;

class ServerController extends ConsoleController
{
    const EXIT_CODE_NO_DOCUMENT_ROOT = 2;
    const EXIT_CODE_NO_ROUTING_FILE = 3;
    const EXIT_CODE_ADDRESS_TAKEN_BY_ANOTHER_SERVER = 4;
    const EXIT_CODE_ADDRESS_TAKEN_BY_ANOTHER_PROCESS = 5;
    /**
     * @var int port to serve on.
     */
    public $port = 8080;
    /**
     * @var string path or path alias to directory to serve
     */
    public $docroot = './web';
    /**
     * @var string path to router script.
     * See https://secure.php.net/manual/en/features.commandline.webserver.php
     */
    public $router;

    /**
     * Runs PHP built-in web server
     *
     * @param string $address address to serve on. Either "host" or "host:port".
     *
     * @return int
     */
    public function index($address = 'localhost')
    {
        $documentRoot = $this->docroot;
        if (strpos($address, ':') === false) {
            $address = $address . ':' . $this->port;
        }
        if (!is_dir($documentRoot)) {
            Lb::app()->stop("Document root \"$documentRoot\" does not exist.\n", self::EXIT_CODE_NO_DOCUMENT_ROOT);
        }
        if ($this->isAddressTaken($address)) {
            Lb::app()->stop("http://$address is taken by another process.\n", self::EXIT_CODE_ADDRESS_TAKEN_BY_ANOTHER_PROCESS);
        }
        if ($this->router !== null && !file_exists($this->router)) {
            Lb::app()->stop("Routing file \"$this->router\" does not exist.\n", self::EXIT_CODE_NO_ROUTING_FILE);
        }
        $this->writeln("Server started on http://{$address}/\n");
        $this->writeln("Document root is \"{$documentRoot}\"\n");
        if ($this->router) {
            $this->writeln("Routing file is \"$this->router\"\n");
        }
        $this->writeln("Quit the server with CTRL-C or COMMAND-C.\n");
        passthru('"' . PHP_BINARY . '"' . " -S {$address} -t \"{$documentRoot}\" $this->router");
    }

    /**
     * @param string $address server address
     * @return bool if address is already in use
     */
    protected function isAddressTaken($address)
    {
        list($hostname, $port) = explode(':', $address);
        $fp = @fsockopen($hostname, $port, $errno, $errstr, 3);
        if ($fp === false) {
            return false;
        }
        fclose($fp);
        return true;
    }
}
