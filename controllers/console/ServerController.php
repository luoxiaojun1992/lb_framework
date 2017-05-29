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
     * @var string host to serve on
     */
    public $host = '127.0.0.1';
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
    public function index()
    {
        $argv = \Console_Getopt::readPHPArgv();
        $opts = \Console_Getopt::getopt(array_slice($argv, 2, count($argv) - 2), 'h::p::d::r::', null, true);
        if (!empty($opts[0]) && is_array($opts[0])) {
            foreach ($opts[0] as $val) {
                if (!empty($val[0]) && !empty($val[1]) && is_string($val[0]) && is_string($val[1])) {
                    switch ($val[0]) {
                        case 'h':
                            $this->host = $val[1];
                            break;
                        case 'p':
                            $this->port = $val[1];
                            break;
                        case 'd':
                            $this->docroot = $val[1];
                            break;
                        case 'r':
                            $this->router = $val[1];
                            break;
                    }
                }
            }
        }

        $documentRoot = $this->docroot;
        $address = $this->host . ':' . $this->port;
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
