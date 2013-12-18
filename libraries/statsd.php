<?php

/*
 * A simple PHP class for interacting with a statsd server
 * @author John Crepezzi <john.crepezzi@gmail.com>
 */
class StatsD {

    private $host, $port;

    // Instantiate a new client
    public function __construct($host = 'localhost', $port = 8125) {
        $this->host = $host;
        $this->port = $port;
    }

    // Record timing
    public function timing($key, $time, $rate = 1) {
        $this->send("$key:$time|ms", $rate);
    }

    // Record gauge
    public function gauge($key, $gauge) {
        $this->send_value("$key:$gauge|g");
    }

    // Time something
    public function time_this($key, $callback, $rate = 1) {
        $begin = microtime(true);
        $callback();
        $time = floor((microtime(true) - $begin) * 1000);
        // And record
        $this->timing($key, $time, $rate);
    }

    // Record counting
    public function counting($key, $amount = 1, $rate = 1) {
        $this->send("$key:$amount|c", $rate);
    }

    // Send
    private function send($value, $rate) {
        $fp = fsockopen('udp://' . $this->host, $this->port, $errno, $errstr);
        // Will show warning if not opened, and return false
        if ($fp) {
            fwrite($fp, "$value|@$rate");
            fclose($fp);
        }
    }
        
    private function send_value($value) {
        $fp = fsockopen('udp://' . $this->host, $this->port, $errno, $errstr);
        // Will show warning if not opened, and return false
        if ($fp) {
            fwrite($fp, "$value");
            fclose($fp);
        }
       
    }

}
