<?php
include_once('config/constant.php');
include("config/configuration.php");

if (!empty($_POST['timestamp'])) {
    $differenceInSeconds = (strtotime('now') + TIMEOUT_TIMESTAMP) - strtotime($obj_security->clear_input($_POST['timestamp']));
    if ($differenceInSeconds < TIMEOUT_TIMESTAMP) {
        $user_name = (isset($_POST['username'])) ? $obj_security->clear_input($_POST['username']) : "";
        $user_hwid = (isset($_POST['hwid'])) ? $obj_security->clear_input($_POST['hwid']) : "";
        $obj_log = new log($db);
        $log_data = array();
        $log_data['log_date'] = date('Y-m-d H:i:s');
        $log_data['log_username'] = $user_name;
        $log_data['log_hwid'] = $user_hwid;
        $log_data['log_summary'] = TIMESTAMP_ERROR;
        $obj_log->insert_log($log_data);
        echo $obj_security->encryptRJ256('TIMESTAMP_ERROR');
        exit;
    } else {
        echo $obj_security->encryptRJ256('TIMESTAMP_GOOD');
        exit;
    }
}