<?php
include_once('config/constant.php');
include("config/configuration.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['rpassword']) || empty($_POST['email']) || empty($_POST['hwid']) || empty($_POST['secret_qus'])) {
        $obj_security = new security($db);
        echo $obj_security->exportEcho('REGISTER_FIELDEMPTY');
        $obj_log = new log($db);
        $log_data = array();
        $log_data['log_date'] = date('Y-m-d H:i:s');
        $log_data['log_username'] = "";
        $log_data['log_hwid'] = "";
        $log_data['log_summary'] = REGISTER_FIELDEMPTY;
        $obj_log->insert_log($log_data);
        exit();
    } else {
        $obj_security = new security($db);
        $user_Data = array();
        $user_Data['user_name'] = (isset($_POST['username'])) ? $obj_security->clear_input($_POST['username']) : "";
        $user_Data['pass_word'] = (isset($_POST['password'])) ? $obj_security->clear_input($_POST['password']) : "";
        $user_Data['rpass_word'] = (isset($_POST['rpassword'])) ? $obj_security->clear_input($_POST['rpassword']) : "";
        $user_Data['user_email'] = (isset($_POST['email'])) ? $obj_security->clear_input($_POST['email']) : "";
        $user_Data['user_hwid'] = (isset($_POST['hwid'])) ? $obj_security->clear_input($_POST['hwid']) : "";
        $user_Data['user_secret'] = (isset($_POST['secret_qus'])) ? $obj_security->clear_input($_POST['secret_qus']) : "";

        if (!$obj_security->clear_input_name($user_Data['user_name'])) {
            $obj_security = new security($db);
            echo $obj_security->exportEcho('REGISTER_USERNAMELENGTH_ERROR');

            $obj_log = new log($db);
            $log_data = array();
            $log_data['log_date'] = date('Y-m-d H:i:s');
            $log_data['log_username'] = $user_Data['user_name'];
            $log_data['log_hwid'] = $user_Data['user_hwid'];
            $log_data['log_summary'] = REGISTER_USERNAMELENGTH_ERROR;
            $obj_log->insert_log($log_data);
            exit();
        } else if (!$obj_security->clear_input_email($user_Data['user_email'])) {
            echo $obj_security->exportEcho('REGISTER_EMAILFORMAT_ERROR');
            $obj_log = new log($db);
            $log_data = array();
            $log_data['log_date'] = date('Y-m-d H:i:s');
            $log_data['log_username'] = $user_Data['user_name'];
            $log_data['log_hwid'] = $user_Data['user_hwid'];
            $log_data['log_summary'] = REGISTER_EMAILFORMAT_ERROR;
            $obj_log->insert_log($log_data);
            exit();
        } else if ($user_Data['pass_word'] != $user_Data['rpass_word']) {
            $obj_security = new security($db);
            echo $obj_security->exportEcho('REGISTER_PASSWORDS_MATCHERROR');
            $obj_log = new log($db);
            $log_data = array();
            $log_data['log_date'] = date('Y-m-d H:i:s');
            $log_data['log_username'] = $user_Data['user_name'];
            $log_data['log_hwid'] = $user_Data['user_hwid'];
            $log_data['log_summary'] = REGISTER_PASSWORDS_MATCHERROR;
            $obj_log->insert_log($log_data);
            exit();
        } else {
            $obj_user = new user($db);    #Calling the User class
            $user_Data['pass_word'] = password_hash($user_Data['pass_word'], PASSWORD_ARGON2I);
            $user_Data['user_id'] = $obj_security->generateUserID(16);
            $user_Data['user_rank'] = 0;
            $user_Data['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $user_Data['user_status'] = 0;
            $user_Data['user_start'] = '';
            $user_Data['user_timeleft'] = 0;
            $user_Data['registered_date'] = date('Y-m-d H:i:s');
            $user_Data['registered_country'] = $obj_user->getLocationInfoByIp();
            $user_Data['registered_useragent'] = $_SERVER['HTTP_USER_AGENT'];
            $checklogin = $obj_user->check_username($user_Data['user_name']);
            if ($checklogin->num_rows > 0) {
                echo $obj_security->exportEcho('REGISTER_EXISTUSERNAME');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_Data['user_name'];
                $log_data['log_hwid'] = $user_Data['user_hwid'];
                $log_data['log_summary'] = REGISTER_EXISTUSERNAME;
                $obj_log->insert_log($log_data);
                exit();
            } else {
                $chk_banned = $obj_user->check_banned($user_Data['user_hwid']);
                if ($chk_banned->num_rows > 0) {
                    $obj_security = new security($db);
                    echo $obj_security->exportEcho('REGISTER_BANNED');
                    $obj_log = new log($db);
                    $log_data = array();
                    $log_data['log_date'] = date('Y-m-d H:i:s');
                    $log_data['log_username'] = $user_Data['user_name'];
                    $log_data['log_hwid'] = $user_Data['user_hwid'];
                    $log_data['log_summary'] = REGISTER_BANNED;
                    $obj_log->insert_log($log_data);
                    exit();
                }
                $stmt = $obj_user->insert_user($user_Data);

                if ($stmt) {
                    $obj_security = new security($db);
                    echo $obj_security->exportEcho('REGISTER_SUCCESSED');
                    $obj_log = new log($db);
                    $log_data = array();
                    $log_data['log_date'] = date('Y-m-d H:i:s');
                    $log_data['log_username'] = $user_Data['user_name'];
                    $log_data['log_hwid'] = $user_Data['user_hwid'];
                    $log_data['log_summary'] = REGISTER_SUCCESSED;
                    $obj_log->insert_log_success($log_data);
                    exit();
                }
            }
        }
    }
}