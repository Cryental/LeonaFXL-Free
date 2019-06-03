<?php
include_once('config/constant.php');
include("config/configuration.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $obj_security = new security($db);
    $user_name = (isset($_POST['username'])) ? $obj_security->clear_input($_POST['username']) : "";
    $user_hwid = (isset($_POST['hwid'])) ? $obj_security->clear_input($_POST['hwid']) : "";
    $obj_user = new user($db);
    $checklogin = $obj_user->check_username($user_name);
    if ($checklogin->num_rows > 0) {
        $obj_user->update_ban($user_name);
        $obj_log = new log($db);
        $log_data = array();
        $log_data['log_date'] = date('Y-m-d H:i:s');
        $log_data['log_username'] = $user_name;
        $log_data['log_hwid'] = $user_hwid;
        $log_data['log_summary'] = AUTO_BANNED;
        $obj_log->insert_log_success($log_data);
    }
}