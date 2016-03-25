<?php namespace backend;

class RunOnce {
    
    private static $strLocalIp = '127.0.0.1';
    private $numProcessId = 0;
    private $objSocket = null;
    
    public function __construct ($numProcessId) {
        $this->numProcessId = $numProcessId;
    }
    
    public function start () {
        
        if (fsockopen(self::$strLocalIp, $this->numProcessId)) {
            exit( 'Another  is running, dying' );
        }

        if (( $this->objSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            exit("socket_create() failed: reason: ".socket_strerror(socket_last_error())."\n");
        }

        if (socket_bind($this->objSocket, self::$strLocalIp, $this->numProcessId) === false) {
            exit("socket_bind() failed: reason: ".socket_strerror(socket_last_error($this->objSocket))."\n");
        }

        if (socket_listen($this->objSocket, 1) === false) {
            exit("socket_listen() failed: reason: ".socket_strerror(socket_last_error($this->objSocket))."\n");
        }
    }
    
    public function end () {
        socket_close($this->objSocket);
    }
    
}