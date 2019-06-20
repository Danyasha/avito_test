<?php
class Router {
    function __construct() {
        $this->routers_get = [];
        $this->routers_post = [];
    }
    public function run() {
        if (isset($_SERVER["REQUEST_URI"])) {
            $url = $_SERVER["REQUEST_URI"];
            if (preg_match("/\?/", $url)) {
                $url = preg_split("/\?/", $url)[0];
            }
            if (isset($this->routers_post[$url])) {
                $f = $this->routers_post[$url];
                $f();
                return ;
            } 
            elseif (isset($this->routers_get[$url])) {
                $f = $this->routers_get[$url];
                $f();
                return ;
            }
            http_response_code(404);
            header('Content-Type: application/json');
        }
        else {
            http_response_code(400);
            header('Content-Type: application/json');
        }
    }
    public function route($type, $url, $action) {
        if ($type == "POST")
            $this->routers_post += [$url => $action];
        if ($type == "GET")
            $this->routers_get += [$url => $action];
    }
}