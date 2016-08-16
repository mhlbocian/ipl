<?php

namespace Ipl\Framework;

/**
 * `Url` class for framework of Ipl
 * 
 * @author Michał Bocian <bocian.michal@outlook.com>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 3.0
 */
class Url {

    const PROTOCOL_HTTP = "http";
    const PROTOCOL_HTTPS = "https";
    const PORT_HTTP = 80;
    const PORT_HTTPS = 443;

    private static $protocol = self::PROTOCOL_HTTP;
    private static $hostname = "localhost";
    private static $port = self::PORT_HTTP;
    private static $path = "/";
    private $file = "";
    private $query = "";

    public static function SetProtocol(string $protocol) {
        self::$protocol = $protocol;
    }

    public static function SetHostname(string $hostname) {
        self::$hostname = $hostname;
    }

    public static function SetPort(string $port) {
        self::$port = $port;
    }

    public static function SetPath(string $path) {
        self::$path = $path;
    }
    
    public static function Factory() {
        return new Url();
    }
    
    /* dynamic methods */

    public function __construct() {
        return $this;
    }

    public function File(string $file) {
        $this->file = $file;
        return $this;
    }

    public function Query(string $query) {
        $this->query = $query;
        return $this;
    }

    public function MakeUrl() {
        $return = self::$protocol . "://" . self::$hostname;

        if (self::$protocol == self::PROTOCOL_HTTP && self::$port == self::PORT_HTTP) {
            
        } else if (self::$protocol == self::PROTOCOL_HTTPS && self::$port == self::PORT_HTTPS) {
            
        } else {
            $return .= ":" . self::$port;
        }

        $return .= self::$path . $this->file . $this->query;
        return $return;
    }

    public function __toString() {
        return $this->MakeUrl();
    }

}
