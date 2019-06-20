<?php

class Users {
    function __construct($name) {
        $this->name = $name;
        $this->content = file_get_contents($name);
        $this->content = json_decode($this->content);
    }
    public function add_value($s) {
        if ($this->content != NULL) {
            $key = array_key_last($this->content);
            $key = intval($key);
            $key++;
            $this->content += [$key => $s];
        }
        else {
            $key = 0;
            $this->content = [$key => $s];
        }
        file_put_contents($this->name, json_encode($this->content));
        return $key;
    }
    public function is_key_exists($s) { //проверяет уникальность ключа
        if ($this->content == []) {
            return FALSE;
        }
        foreach($this->content as $val) {
            if ($s === $val)
                return TRUE;
        }
        return FALSE;
    }
    public function get_value($id) {
        if (isset($this->content[$id]))
            return $this->content[$id];
        return NULL;
    }
}