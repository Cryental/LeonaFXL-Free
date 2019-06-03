<?php
include_once('config/constant.php');
include("config/configuration.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) == "" || isset($_POST['email']) == "" || isset($_POST['secret_quest']) == "" || isset($_POST['newpassword']) == "" || isset($_POST['rnewpassword']) == "") {
        $obj_security = new security($db);
        echo $obj_security->exportEcho('FORGOTPWD_FIELDEMPTY');
        $obj_log = new log($db);
        $log_data = array();
        $log_data['log_date'] = date('Y-m-d H:i:s');
        $log_data['log_username'] = "";
        $log_data['log_hwid'] = "";
        $log_data['log_summary'] = FORGOTPWD_FIELDEMPTY;
        $obj_log->insert_log($log_data);
        exit();
    } else {
        $obj_security = new security($db);
        $user_name = $obj_security->clear_input($_POST['username']);
        $user_email = $obj_security->clear_input($_POST['email']);
        $user_secret = $obj_security->clear_input($_POST['secret_quest']);
        $user_pass = $obj_security->clear_input($_POST['newpassword']);
        $user_rpass = $obj_security->clear_input($_POST['rnewpassword']);
        $user_hwid = $obj_security->clear_input($_POST['hwid']);
        if ($user_pass != $user_rpass) {
            $obj_security = new security($db);
            echo $obj_security->exportEcho('FORGOTPWD_PASSWORDS_MATCHERROR');
            $obj_log = new log($db);
            $log_data = array();
            $log_data['log_date'] = date('Y-m-d H:i:s');
            $log_data['log_username'] = $user_name;
            $log_data['log_hwid'] = $user_hwid;
            $log_data['log_summary'] = FORGOTPWD_PASSWORDS_MATCHERROR;
            $obj_log->insert_log($log_data);
            exit();
        } else {
            $obj_user = new user($db);
            $checklogin = $obj_user->check_username($user_name);
            if ($checklogin->num_rows > 0) {
                $getuser = $checklogin->fetch_object();
                if ($getuser->secret_answer != $user_secret) {
                    $obj_security = new security($db);
                    echo $obj_security->exportEcho('FORGOTPWD_ANSWERERROR');
                    $obj_log = new log($db);
                    $log_data = array();
                    $log_data['log_date'] = date('Y-m-d H:i:s');
                    $log_data['log_username'] = $user_name;
                    $log_data['log_hwid'] = $user_hwid;
                    $log_data['log_summary'] = FORGOTPWD_ANSWERERROR;
                    $obj_log->insert_log($log_data);
                    exit();
                } else if ($getuser->email != $user_email) {
                    $obj_security = new security($db);
                    echo $obj_security->exportEcho('FORGOTPWD_EMAILINCORRECT');
                    $obj_log = new log($db);
                    $log_data = array();
                    $log_data['log_date'] = date('Y-m-d H:i:s');
                    $log_data['log_username'] = $user_name;
                    $log_data['log_hwid'] = $user_hwid;
                    $log_data['log_summary'] = FORGOTPWD_EMAILINCORRECT;
                    $obj_log->insert_log($log_data);
                    exit();
                } else {
                    $obj_user = new user($db);
                    $hashPassword = password_hash($user_pass, PASSWORD_ARGON2I, $options);
                    $stmt = $obj_user->update_passwd($user_name, $hashPassword, PASSWORD_ARGON2I, $options);
                    if ($stmt) {
                        $obj_security = new security($db);
                        echo $obj_security->exportEcho('FORGOTPWD_SUCCESSED');
                        $obj_log = new log($db);
                        $log_data = array();
                        $log_data['log_date'] = date('Y-m-d H:i:s');
                        $log_data['log_username'] = $user_name;
                        $log_data['log_hwid'] = $user_hwid;
                        $log_data['log_summary'] = FORGOTPWD_SUCCESSED;
                        $obj_log->insert_log_success($log_data);
                        $stmt->close();
                        exit();
                    }
                }
            } else {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('FORGOTPWD_INCORRECT');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = FORGOTPWD_INCORRECT;
                $obj_log->insert_log($log_data);
                exit();
            }
        }
    }
}