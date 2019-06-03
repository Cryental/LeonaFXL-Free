<?php
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
} catch (Exception $e) {
    echo "Service unavailable";
    exit;
}

try {
    $db->set_charset("utf8");
} catch (Exception $e) {
    echo "Service unavailable";
    exit;
}

error_reporting(0);

spl_autoload_register(function ($class_name) {
    include "class/" . $class_name . '.php';
});

$obj_security = new security($db);
$obj_license = new license($db);
$obj_user = new user($db);

if (!empty($_POST['c_key'])) {
    $_POST['c_key'] = $obj_security->clear_input($_POST['c_key']);
    if ($_POST['c_key'] != COMMUNICATION_KEY) {
        echo $obj_security->exportEcho('INVALID_ACCESS');
        $user_name = (isset($_POST['username'])) ? $obj_security->clear_input($_POST['username']) : "";
        $user_hwid = (isset($_POST['hwid'])) ? $obj_security->clear_input($_POST['hwid']) : "";
        $obj_log = new log($db);
        $log_data = array();
        $log_data['log_date'] = date('Y-m-d H:i:s');
        $log_data['log_username'] = $user_name;
        $log_data['log_hwid'] = $user_hwid;
        $log_data['log_summary'] = INVALID_ACCESS;
        $obj_log->insert_log($log_data);
        exit;
    }
}
