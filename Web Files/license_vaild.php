<?php
include_once('config/constant.php');
include("config/configuration.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['hwid']) || empty($_POST['license'])) {
        $obj_security = new security($db);
        echo $obj_security->exportEcho('LICENSEVAILD_FIELDEMPTY');
        $obj_log = new log($db);
        $log_data = array();
        $log_data['log_date'] = date('Y-m-d H:i:s');
        $log_data['log_username'] = "";
        $log_data['log_hwid'] = "";
        $log_data['log_summary'] = LICENSEVAILD_FIELDEMPTY;
        $obj_log->insert_log($log_data);
        exit();
    } else {
        $obj_security = new security($db);
        $user_name = $obj_security->clear_input($_POST['username']);
        $user_hwid = $obj_security->clear_input($_POST['hwid']);
        $user_license = $obj_security->clear_input($_POST['license']);
        $obj_user = new user($db);
        $checklogin = $obj_user->check_username($user_name);
        if ($checklogin->num_rows > 0) {
            $currenttime = date('Y-m-d H:i:s');
            $getuser = $checklogin->fetch_object();
            $obj_security = new security($db);
            if (!password_verify($obj_security->clear_input($_POST['password']), $getuser->password)) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LICENSEVAILD_INCORRECT');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LICENSEVAILD_INCORRECT;
                $obj_log->insert_log($log_data);
                exit();
            } else if ($getuser->hwid != $user_hwid) {
                $obj_security = new security($db);
                echo $obj_security->exportEcho('LICENSEVAILD_HARDWAREIDERROR');
                $obj_log = new log($db);
                $log_data = array();
                $log_data['log_date'] = date('Y-m-d H:i:s');
                $log_data['log_username'] = $user_name;
                $log_data['log_hwid'] = $user_hwid;
                $log_data['log_summary'] = LICENSEVAILD_HARDWAREIDERROR;
                $obj_log->insert_log($log_data);
                exit();
            } else {
                $obj_license = new license($db);
                $checklicense = $obj_license->check_license($user_license);
                if ($checklicense->num_rows > 0) {
                    $licensedet = $checklicense->fetch_object();
                    if ($licensedet->status == '1') {
                        $obj_security = new security($db);
                        echo $obj_security->exportEcho('LICENSEVAILD_USEDLICENSE');
                        $obj_log = new log($db);
                        $log_data = array();
                        $log_data['log_date'] = date('Y-m-d H:i:s');
                        $log_data['log_username'] = $user_name;
                        $log_data['log_hwid'] = $user_hwid;
                        $log_data['log_summary'] = LICENSEVAILD_USEDLICENSE;
                        $obj_log->insert_log($log_data);
                        exit();
                    } else if ($licensedet->status == '2') {
                        $obj_security = new security($db);
                        echo $obj_security->exportEcho('LICENSEVAILD_BANNED');
                        $obj_log = new log($db);
                        $log_data = array();
                        $log_data['log_date'] = date('Y-m-d H:i:s');
                        $log_data['log_username'] = $user_name;
                        $log_data['log_hwid'] = $user_hwid;
                        $log_data['log_summary'] = LICENSEVAILD_BANNED;
                        $obj_log->insert_log($log_data);
                        exit();
                    } else if ($licensedet->status == '0') {
                        $obj_license = new license($db);
                        $stmt = $obj_license->check_license('1', $user_license);
                        if ($stmt) {
                            if ($getuser->rank == 1) {
                                $obj_security = new security($db);
                                echo $obj_security->exportEcho('LICENSEVAILD_LIFETIMEALEADY');
                                $obj_log = new log($db);
                                $log_data = array();
                                $log_data['log_date'] = date('Y-m-d H:i:s');
                                $log_data['log_username'] = $user_name;
                                $log_data['log_hwid'] = $user_hwid;
                                $log_data['log_summary'] = LICENSEVAILD_LIFETIMEALEADY;
                                $obj_log->insert_log($log_data);
                                exit();
                            } else {
                                if ($licensedet->lifetime == 1) {
                                    if ($getuser->startdate == "") {
                                        $obj_user = new user($db);
                                        $stmt = $obj_user->update_account(date("Y-m-d H:i:s"), $licensedet->timeleft, '1', $user_name);
                                        $obj_log = new log($db);
                                        $log_data = array();
                                        $log_data['log_date'] = date('Y-m-d H:i:s');
                                        $log_data['log_username'] = $user_name;
                                        $log_data['log_hwid'] = $user_hwid;
                                        $log_data['log_summary'] = LICENSEVAILD_UPDATE_ACCOUNT;
                                        $obj_log->insert_log_success($log_data);
                                    } else {
                                        $timeleft = date('Y-m-d H:i:s', strtotime($getuser->startdate . ' + ' . $getuser->timeleft . ' days'));
                                        $now_currenttime = date('Y-m-d H:i:s');
                                        if ($timeleft < $now_currenttime) {
                                            $obj_user = new user($db);
                                            $stmt = $obj_user->update_account(date("Y-m-d H:i:s"), $licensedet->timeleft, '1', $user_name);
                                            $obj_log = new log($db);
                                            $log_data = array();
                                            $log_data['log_date'] = date('Y-m-d H:i:s');
                                            $log_data['log_username'] = $user_name;
                                            $log_data['log_hwid'] = $user_hwid;
                                            $log_data['log_summary'] = LICENSEVAILD_UPDATE_ACCOUNT;
                                            $obj_log->insert_log_success($log_data);
                                        } else {
                                            $obj_user = new user($db);
                                            $stmt = $obj_user->update_account2($getuser->timeleft + $licensedet->timeleft, '1', $user_name);

                                            $obj_log = new log($db);
                                            $log_data = array();
                                            $log_data['log_date'] = date('Y-m-d H:i:s');
                                            $log_data['log_username'] = $user_name;
                                            $log_data['log_hwid'] = $user_hwid;
                                            $log_data['log_summary'] = LICENSEVAILD_UPDATE_ACCOUNT;
                                            $obj_log->insert_log_success($log_data);
                                        }
                                    }
                                } else {
                                    if ($getuser->startdate == "") {
                                        $obj_user = new user($db);
                                        $stmt = $obj_user->update_account3(date("Y-m-d H:i:s"), $licensedet->timeleft, $user_name);

                                        $obj_log = new log($db);
                                        $log_data = array();
                                        $log_data['log_date'] = date('Y-m-d H:i:s');
                                        $log_data['log_username'] = $user_name;
                                        $log_data['log_hwid'] = $user_hwid;
                                        $log_data['log_summary'] = LICENSEVAILD_UPDATE_ACCOUNT;
                                        $obj_log->insert_log_success($log_data);
                                    } else {
                                        $db_licenseexpired = date('Y-m-d H:i:s', strtotime($getuser->startdate . ' + ' . $getuser->timeleft . ' days'));
                                        $now_currenttime = date('Y-m-d H:i:s');
                                        if ($db_licenseexpired < $now_currenttime) {
                                            $obj_user = new user($db);
                                            $stmt = $obj_user->update_account3(date("Y-m-d H:i:s"), $licensedet->timeleft, $user_name);
                                            $obj_log = new log($db);
                                            $log_data = array();
                                            $log_data['log_date'] = date('Y-m-d H:i:s');
                                            $log_data['log_username'] = $user_name;
                                            $log_data['log_hwid'] = $user_hwid;
                                            $log_data['log_summary'] = LICENSEVAILD_UPDATE_ACCOUNT;
                                            $obj_log->insert_log_success($log_data);
                                        } else {
                                            $obj_user = new user($db);
                                            $stmt = $obj_user->update_account3($getuser->startdate, $getuser->timeleft + $licensedet->timeleft, $user_name);
                                            $obj_log = new log($db);
                                            $log_data = array();
                                            $log_data['log_date'] = date('Y-m-d H:i:s');
                                            $log_data['log_username'] = $user_name;
                                            $log_data['log_hwid'] = $user_hwid;
                                            $log_data['log_summary'] = LICENSEVAILD_UPDATE_ACCOUNT;
                                            $obj_log->insert_log_success($log_data);
                                        }
                                    }
                                }
                            }
                            $obj_user1 = new license($db);
                            $stmts = $obj_user1->update_license(1, $user_license);
                            $obj_security = new security($db);
                            echo $obj_security->exportEcho('LICENSEVAILD_ACTIVATED');
                            $obj_log = new log($db);
                            $log_data = array();
                            $log_data['log_date'] = date('Y-m-d H:i:s');
                            $log_data['log_username'] = $user_name;
                            $log_data['log_hwid'] = $user_hwid;
                            $log_data['log_summary'] = LICENSEVAILD_ACTIVATED;
                            $obj_log->insert_log_success($log_data);
                            exit();
                        }
                    } else {
                        $obj_security = new security($db);
                        echo $obj_security->exportEcho('LICENSEVAILD_CANTVAILD');
                        $obj_log = new log($db);
                        $log_data = array();
                        $log_data['log_date'] = date('Y-m-d H:i:s');
                        $log_data['log_username'] = $user_name;
                        $log_data['log_hwid'] = $user_hwid;
                        $log_data['log_summary'] = LICENSEVAILD_CANTVAILD;
                        $obj_log->insert_log($log_data);
                        exit();
                    }
                } else {
                    $obj_security = new security($db);
                    echo $obj_security->exportEcho('LICENSEVAILD_CANTFIND');
                    $obj_log = new log($db);
                    $log_data = array();
                    $log_data['log_date'] = date('Y-m-d H:i:s');
                    $log_data['log_username'] = $user_name;
                    $log_data['log_hwid'] = $user_hwid;
                    $log_data['log_summary'] = LICENSEVAILD_CANTFIND;
                    $obj_log->insert_log($log_data);
                    exit();
                }
            }
        } else {
            $obj_security = new security($db);
            echo $obj_security->exportEcho('LICENSEVAILD_INCORRECT');
            $obj_log = new log($db);
            $log_data = array();
            $log_data['log_date'] = date('Y-m-d H:i:s');
            $log_data['log_username'] = $user_name;
            $log_data['log_hwid'] = $user_hwid;
            $log_data['log_summary'] = LICENSEVAILD_INCORRECT;
            $obj_log->insert_log($log_data);
            exit();
        }
    }
}