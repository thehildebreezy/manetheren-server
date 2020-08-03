<?php
/**
 * Use this helper class to send serial data to Xbee connected stuff
 * @author Tanner Hildebrand
 */
// lets start with dimming the serial connected links

class SerialConnection
{
    // property declaration
    const SERIAL_SERVER_PORT = 50999;

    private $socket = false;

    private function makeSocket(){

        if( $this->socket ) return $this->socket;

        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if( !$sock ){ 
            echo "Serial connect failed on socket create";
            return false; 
        }
        $connected = socket_connect($sock, 'localhost', SerialConnection::SERIAL_SERVER_PORT);    
        if( !$connected ){ 
            echo "Serial connect failed on socket connect";
            return false; 
        }

        $this->socket = $sock;
        return $this->socket;
    }

    private function sendString( $message ){
        if( !$this->makeSocket() ){ return false; }
        $n = socket_send( $this->socket, $message, strlen($message), MSG_EOF);
        if( !$n ){
            echo "Serial connect failed on socket send\n";
        }
    }

    // method declaration
    public function sendOther( $message ) {
        
        if( !$this->makeSocket() ) {
            return false;
        }
        
        // big endian message length
        $len = pack("N",strlen($message));

        $msg = "\x00\xFA".$len."\x07".$message;
        $this->sendString($msg);
    }
}
?>

