<?php
include_once('config/constant.php');
include("config/configuration.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) == "" || isset($_POST['password']) == "" || isset($_POST['hwid']) == "" || isset($_POST['timeleft']) == "" || isset($_POST['lifetime']) == "") {
        $obj_security = new security($db);
        echo $obj_security->exportEcho("LICENSEREG_FIELDEMPTY");

        $obj_log = new log($db);
        $log_data = array();
        $log_data['log_date'] = date('Y-m-d H:i:s');
        $log_data['log_username'] = "";
        $log_data['log_hwid'] = "";
        $log_data['log_summary'] = LICENSEREG_FIELDEMPTY;
        $obj_log->insert_log($log_data);
        exit();
    } else {
        $obj_security = new security($db);
        $user_name = $obj_security->clear_input($_POST['username']);
        $user_hwid = $obj_security->clear_input($_POST['hwid']);
        $user_timeleft = $obj_security->clear_input($_POST['timeleft']);
        $user_lifetime = $obj_security->clear_input($_POST['lifetime']);
        $obj_user = new user($db);
        $checklogin = $obj_user->check_username($user_name);
        if ($checklogin->num_rows > 0) {
            $currenttime = date('Y-m-d H:i:s');
            $getuser = $checklogin->fetch_object();
            $obj_security = new security($db);
            if (!password_verify($obj_security->clear_input($_POST['password']), $getuser->password)) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho("LICENSEREG_INCORRECT");
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LICENSEREG_INCORRECT;
                $obj_log->insert_log($log_data);
                exit();
            } else if ($getuser->hwid != $user_hwid) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho("LICENSEREG_HARDWAREIDERROR");

                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LICENSEREG_HARDWAREIDERROR;
                $obj_log->insert_log($log_data);
                exit();
            } else if (!is_numeric($user_timeleft)) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho("LICENSEREG_TIMELEFTERROR");

                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LICENSEREG_TIMELEFTERROR;
                $obj_log->insert_log($log_data);
                exit();
            } else if ($getuser->rank != '2') {
                $obj_security = new security($db);
                echo $obj_security->exportEcho("LICENSEREG_NOTADMIN");

                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LICENSEREG_NOTADMIN;
                $obj_log->insert_log($log_data);
                exit();
            } else if ($user_lifetime != 0 && $user_lifetime != 1) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho("LICENSEREG_LIFETIMEERROR");

                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LICENSEREG_LIFETIMEERROR;
                $obj_log->insert_log($log_data);
                exit();
            } else {
                $obj_security = new security($db);
                $licensep1 = $obj_security->generateRandomString();
                $licensep2 = $obj_security->generateRandomString();
                $licensep3 = $obj_security->generateRandomString();
                $licensep4 = $obj_security->generateRandomString();
                $licensekey = strtoupper($licensep1) . '-' . strtoupper($licensep2) . '-' . strtoupper($licensep3) . '-' . strtoupper($licensep4);
                $obj_license = new license($db);
                $stmt = $obj_license->insert_license($licensekey, htmlspecialchars($user_timeleft), 0, htmlspecialchars($user_lifetime), date("Y-m-d H:i:s"), htmlspecialchars($getuser->id));
                if ($stmt) {
                    echo $obj_security->exportEcho($licensekey);
                    $obj_log = new log($db);
                    $log_data = array();
                    $log_data['log_date'] = date('Y-m-d H:i:s');
                    $log_data['log_username'] = $user_name;
                    $log_data['log_hwid'] = $user_hwid;
                    $log_data['log_summary'] = LICENSEREG_CREATED;
                    $obj_log->insert_log_success($log_data);
                    $stmt->close();
                    exit();
                }
            }
        } else {
            $obj_security = new security($db);
            echo $obj_security->exportEcho("LICENSEREG_INCORRECT");
            $obj_log = new log($db);
            $log_data = array();
            $log_data['log_date'] = date('Y-m-d H:i:s');
            $log_data['log_username'] = $user_name;
            $log_data['log_hwid'] = $user_hwid;
            $log_data['log_summary'] = LICENSEREG_INCORRECT;
            $obj_log->insert_log($log_data);
            exit();
        }
    }
}