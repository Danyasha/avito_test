<?php
class Random_value_generator {
    function __construct($type, $len, $from_values)
    {
        $this->type = $type;
        $this->from_values = $from_values;
        $this->len = $len;
        $this->types = ["string" => 1, "number" => 1, "GUID" => 1, "from" => 1];
        $this->minlen = 10;
        $this->maxlen = 100;
        $this->min_len_from = 5;
        $this->check();
    }

    public function get_value() {
        if ($this->type === "GUID") {
            return ($this->getGUID());
        }
        if ($this->type === "from") {
            return ($this->generate_from_values());
        }
        $fd = fopen("/dev/urandom", "r");
        $value = "";
        while ($this->len > strlen($value)) {
            $value = $value . hash("md5", fgets($fd, 25));
        }
        $value = substr($value, 0, $this->len);
        if ($this->type === "number") {
            return ($this->to_number($value));
        }
        fclose ($fd);
        return ($value);
    }
    private function check() {
        if (!isset($this->types[$this->type])) {
            throw new Exception('Unsupported type');
            return FALSE;
        }
        if ($this->len < 10) {
            throw new Exception('Small_len');
            return FALSE;
        }
        if ($this->len > 100) {
            throw new Exception('Big_len');
            return FALSE;
            }
        if ($this->type === "from" && $this->from_values == "") {
                throw new Exception('from_values not defined');
                return FALSE;
        }
        if ($this->type === "from" && strlen($this->from_values) < 4) {
            throw new Exception('Small_from');
            return FALSE;
        }
        if ($this->type === "from") {
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
    private function getGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }
        else{
            mt_srand((double)microtime()*10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            return $uuid;
        }
    }
    private function generate_from_values() {
        $value = "";
        for ($i = 0; $i < $this->len; $i++) {
            $value = $value . $this->from_values[random_int(0, strlen($this->from_values) - 1)];
        }
        return $value;
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
}