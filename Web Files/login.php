<?php
include_once('config/constant.php');
include("config/configuration.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['hwid'])) {
        $obj_security = new security($db);
        echo $obj_security->exportEcho("LOGIN_FIELDEMPTY");
        exit();
    } else {
        $obj_security = new security($db);
        $user_name = (isset($_POST['username'])) ? $obj_security->clear_input($_POST['username']) : "";
        $user_hwid = (isset($_POST['hwid'])) ? $obj_security->clear_input($_POST['hwid']) : "";
        $obj_user = new user($db);
        $checklogin = $obj_user->check_username($user_name);
        if ($checklogin->num_rows > 0) {
            $currenttime = date('Y-m-d H:i:s');
            $getuser = $checklogin->fetch_object();
            $timeleft = date('Y-m-d H:i:s', strtotime($getuser->startdate . ' + ' . $getuser->timeleft . ' days'));
            $obj_security = new security($db);
            if (!password_verify($obj_security->clear_input($_POST['password']), $getuser->password)) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LOGIN_INCORRECT');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LOGIN_INCORRECT;
                $obj_log->insert_log($log_data);
                exit();
            } else if ($getuser->status == '1') {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LOGIN_BANNED');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LOGIN_BANNED;
                $obj_log->insert_log($log_data);
                exit();
            } else if ($getuser->hwid_status == '1' && $getuser->hwid != $user_hwid) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LOGIN_HARDWAREIDERROR');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LOGIN_HARDWAREIDERROR;
                $obj_log->insert_log($log_data);
                exit();
            } else if ($getuser->rank != '2' && $getuser->startdate == "") {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LOGIN_LICENSEEMPTY');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LOGIN_LICENSEEMPTY;
                $obj_log->insert_log($log_data);
                exit();
            } else if ($getuser->rank == '1') {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LOGIN_GRANTED_LIFETIME');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LOGIN_GRANTED_LIFETIME;
                $obj_log->insert_log_success($log_data);
                exit();
            } else if ($getuser->rank == '2') {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LOGIN_GRANTED_ADMIN');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LOGIN_GRANTED_ADMIN;
                $obj_log->insert_log_success($log_data);
                exit();
            } else if ($timeleft > $currenttime || $timeleft == $currenttime) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LOGIN_GRANTED_USER');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LOGIN_GRANTED_USER;
                $obj_log->insert_log_success($log_data);
                exit();
            } else if ($timeleft < $currenttime) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LOGIN_LICENSEEXPIRED');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LOGIN_LICENSEEXPIRED;
                $obj_log->insert_log($log_data);
                exit();
            } else {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LOGIN_LICENSEEMPTY');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LOGIN_LICENSEEMPTY;
                $obj_log->insert_log($log_data);
                exit();
            }
        } else {
            $obj_security = new security($db);
            echo $obj_security->exportEcho('LOGIN_INCORRECT');
            $obj_log = new log($db);
            $log_data = array();
            $log_data['log_date'] = date('Y-m-d H:i:s');
            $log_data['log_username'] = $user_name;
            $log_data['log_hwid'] = $user_hwid;
            $log_data['log_summary'] = LOGIN_INCORRECT;
            $obj_log->insert_log($log_data);
            exit();
        }
    }
}