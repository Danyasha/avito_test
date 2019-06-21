<?php

class Fabric_random {
    function __construct($type, $len, $from_values = NULL) {
        $types = ["string" => 1, "numeric" => 1, "GUID" => 1, "from" => 1];
        $this->check($type, $types);
        if ($type == "string") {
            return New Random_string_generator($len);
        }
        if ($type == "GUID") {
            return new Guid_generator($len);
        }
        if ($type == "numeric") {
            return New Random_numberic_generator($len);
        }
        if ($type == "from") {
            return New Random_guid_generator($len, $from_values);
        }
    }
    private function check($type, $types) {
        if (!isset($types[$type])) {
            throw new Exception('Unsupported type');
            return FALSE;
        }
    }
}

abstract class Random_value_generator {
    function __construct($type, $len, $from_values = NULL)
    {
        $this->type = $type;
        $this->from_values = $from_values;
        if ($len == NULL)
            $len = 40;
        $this->len = $len;
        $this->minlen = 10;
        $this->maxlen = 100;
        $this->min_len_from = 5;
    }
    abstract public function get_value();
    protected function check() {
        if ($this->len < 10) {
            throw new Exception('Small_len');
            return FALSE;
            }
        if ($this->len > 100) {
            throw new Exception('Big_len');
            return FALSE;
            }
        }
}
class Random_string_generator extends Random_value_generator {
    function __construct($len) {
        parent::__construct("string", $len, NULL);
        parent::check();
        $fd = fopen("/dev/urandom", "r");
        $this->random_bytes = "";
        while ($this->len > strlen($this->random_bytes)) {
            $this->random_bytes = $this->random_bytes . hash("md5", fgets($fd, 25));
        }
        $this->random_bytes = substr($this->random_bytes, 0, $this->len);
        fclose ($fd);
    }
    public function get_value() {
        return($this->random_bytes);
    }
}

class Random_numberic_generator extends Random_string_generator {
    function __construct( $len) {
        parent::__construct("numeric", $len, NULL);
        parent::check();
    }
    private function to_number($s) {
        $num = "";
        for ($i = 0; $i < strlen($s); $i++) {
            if (!preg_match("/[0-9]/", $s[$i]))
                $num = $num . (ord($s[$i]) % 10);
            else
                $num = $num . $s[$i];
        }
        return ($num);
    }
    public function get_value() {
        return(to_number($this->random_bytes));
    }
}

class From_values_generator extends Random_value_generator {
    function __construct($len, $from_values) {
        parent::__construct("from", $len, $from_values);
        parent::check();
        $this->check();
    }
    public function get_value() {
        $value = "";
        for ($i = 0; $i < $this->len; $i++) {
            $value = $value . $this->from_values[random_int(0, strlen($this->from_values) - 1)];
        }
        return $value;
    }
    protected function check() {
        if ($this->from_values == "" || $this->from_values == NULL) {
            throw new Exception('from_values not defined');
            return FALSE;
        }
        if (strlen($this->from_values) < 4) {
            throw new Exception('Small_from');
            return FALSE;
        }
        $was = "";
        for ($i = 0; $i < strlen($this->from_values); $i++) {
            if (strpos($was, $this->from_values[$i]) !== FALSE) {
                throw new Exception('duplicate_symbols');
                return FALSE;
            }
            $was = $was . $this->from_values[$i];
        }
    }
}

class Random_guid_generator extends Random_value_generator {
    function __construct($len) {
        parent::__construc("GUID", $len, NULL);
        parent::check();
    }
    public function get_value() { //https://www.php.net/manual/ru/function.com-create-guid.php 
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
            return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}
