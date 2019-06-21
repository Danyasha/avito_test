<?php
include_once 'Generate_v_2.php';
include_once 'Db_controller.php';

function get_value() {
    if (!isset($_GET["id"])) {
        write_error_response(400, "");
        return ;
    }
    if (is_numeric($_GET["id"]) && intval($_GET["id"]) >= 0) {
        $users = new Users("users.json");
        $value = $users->get_value(intval($_GET["id"]));
        if ($value == NULL) {
            write_error_response(404, "Id not exist");
        }
        else {
            write_ok_response($_GET["id"], $value);
        }
    }
    else {
        write_error_response(400, "Invalid id");
        return ;
    }

}

function generate_value() {
    if (!check_values($_POST)) {
        write_error_response(400, "");
        return ;
    }
    $len = 40;
    $type = "string";
    $from_values = "";
    if (isset($_POST["len"]) && is_numeric($_POST["len"])) {
        $len = intval($_POST["len"]);
    }
    if (isset($_POST["type"])) {
        $type = $_POST["type"];
    }
    if (isset($_POST["from_values"])) {
        $from_values = $_POST["from_values"];
    }
    try {
        $r = new Fabric_random();
        $r = $r->get_Random($type, $len, $from_values);
        $value = $r->get_value();
        $users = new Users("users.json");
        while ($users->is_key_exists($value)) {
            $value = $r->get_value();
        }
        $id = $users->add_value($value);
        write_ok_response($id, $value);
    }
    catch (Exception $e){
        write_error_response(400,  $e->getMessage());
    }

}

function write_ok_response($id, $value) {
    header('Content-Type: application/json');
    $jsoned = json_encode(["id" => $id, "value" => $value]);
    http_response_code(200);
    print_r($jsoned);
}
function write_error_response($code, $text) {
    http_response_code($code);
    if ($text !== "") {
    $message = ["message" => $text];
    $jsoned = json_encode($message);
    print_r($jsoned);
    }
}

function get_random_value($type = "string", $len = 40, $from_values = "") {
    $v = new Random_value_generator($type, $len, $from_values);
    return($v->get_value());
}

function check_values($arr) {
    if (!isset($arr["type"]))
        return TRUE;
    if (isset($arr["type"]) && $arr["type"] === "from" && isset($arr["from_values"])) {
        return TRUE;
    }
    if ($arr["type"] !== "from")
        return TRUE;
    return FALSE;
}