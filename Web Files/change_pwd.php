<?php
include_once('config/constant.php');
include("config/configuration.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) == "" || isset($_POST['oldpassword']) == "" || isset($_POST['newpassword']) == "" || isset($_POST['rnewpassword']) == "" || isset($_POST['hwid']) == "") {
        $obj_security = new security($db);
        echo $obj_security->exportEcho("CHANGEPWD_FIELDEMPTY");
        $obj_log = new log($db);
        $log_data = array();
        $log_data['log_date'] = date('Y-m-d H:i:s');
        $log_data['log_username'] = "";
        $log_data['log_hwid'] = "";
        $log_data['log_summary'] = CHANGEPWD_FIELDEMPTY;
        $obj_log->insert_log($log_data);
        exit();
    } else {
        $obj_security = new security($db);
        $user_name = $obj_security->clear_input($_POST['username']);
        $user_npass = $obj_security->clear_input($_POST['newpassword']);
        $user_nrpass = $obj_security->clear_input($_POST['rnewpassword']);
        $user_hwid = $obj_security->clear_input($_POST['hwid']);

        $obj_user = new user($db);
        $checklogin = $obj_user->check_username($user_name);
        if ($checklogin->num_rows > 0) {
            $getuser = $checklogin->fetch_object();
            if (!password_verify($obj_security->clear_input($_POST['oldpassword']), $getuser->password)) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho("CHANGEPWD_INCORRECT");
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = CHANGEPWD_INCORRECT;
                $obj_log->insert_log($log_data);
                exit();
            } else if ($getuser->hwid != $user_hwid) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho("CHANGEPWD_HARDWAREIDERROR");
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = CHANGEPWD_HARDWAREIDERROR;
                $obj_log->insert_log($log_data);
                exit();
            } else if ($user_npass != $user_nrpass) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho("CHANGEPWD_PASSWORDS_MATCHERROR");
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = CHANGEPWD_PASSWORDS_MATCHERROR;
                $obj_log->insert_log($log_data);
                exit();
            } else {
                $obj_user = new user($db);
                $hashPassword = password_hash($user_npass, PASSWORD_ARGON2I, $options);
                $stmt = $obj_user->update_passwd($user_name, $hashPassword, PASSWORD_ARGON2I, $options);

                if ($stmt) {
                    $obj_security = new security($db);
                    echo $obj_security->exportEcho("CHANGEPWD_SUCCESSED");
                    $obj_log = new log($db);
                    $log_data = array();
                    $log_data['log_date'] = date('Y-m-d H:i:s');
                    $log_data['log_username'] = $user_name;
                    $log_data['log_hwid'] = $user_hwid;
                    $log_data['log_summary'] = CHANGEPWD_SUCCESSED;
                    $obj_log->insert_log_success($log_data);
                    $stmt->close();
                    exit();
                }
            }
        } else {
            $obj_security = new security($db);
            echo $obj_security->exportEcho("CHANGEPWD_CANTFIND");

            $obj_log = new log($db);
            $log_data = array();
            $log_data['log_date'] = date('Y-m-d H:i:s');
            $log_data['log_username'] = $user_name;
            $log_data['log_hwid'] = $user_hwid;
            $log_data['log_summary'] = CHANGEPWD_CANTFIND;
            $obj_log->insert_log($log_data);
            exit();
        }
    }
}