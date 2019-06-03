<?php
class user
{
    private $_db = "";
    function __construct($db)
    {
        $this->_db = $db;
    }

    function check_username($user_name)
    {
        $sql = "SELECT * FROM `leonafx_accounts` WHERE `username` = '$user_name'";
        $result = $this->_db->query($sql);
        return $result;
    }

    function check_banned($user_hwid)
    {
        $sql = "SELECT * FROM `leonafx_accounts` WHERE `hwid` = '$user_hwid' AND `status`='1'";
        $result = $this->_db->query($sql);
        return $result;
    }

    function insert_user($user_data)
    {
        $user_Data['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $sql = "INSERT INTO `leonafx_accounts`(`user_id`, `username`, `password`, `hwid`, `email`, `ip`, `rank`, `status`, `startdate`, `timeleft`, `secret_answer`, `registered_date`, `registered_country`, `registered_useragent`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("ssssssssssssss", $user_data['user_id'], $user_data['user_name'], $user_data['pass_word'], $user_data['user_hwid'], $user_data['user_email'], $user_data['user_ip'], $user_data['user_rank'], $user_data['user_status'], $user_data['user_start'], $user_data['user_timeleft'], $user_data['user_secret'], $user_data['registered_date'], $user_data['registered_country'], $user_data['registered_useragent']);
        $stmt->execute();
        return $stmt;
    }

    function update_ban($user_name)
    {
        $sql = "UPDATE `leonafx_accounts` SET status = ? WHERE username = ?";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("ss", '1', $user_name);
        $stmt->execute();
        return $stmt;
    }

    function update_passwd($user_name, $user_npass)
    {
        $sql = "UPDATE `leonafx_accounts` SET password = ? WHERE username = ?";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("ss", $user_npass, $user_name);
        $stmt->execute();
        return $stmt;
    }

    function update_account($lc_start, $lc_endtime, $lc_rank, $lc_username)
    {
        $sql = "UPDATE `leonafx_accounts` SET startdate = ?, timeleft = ?, rank = ? WHERE username = ?";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("ssss", $lc_start, $lc_endtime, $lc_rank, $lc_username);
        $stmt->execute();
        return $stmt;
    }

    function update_account2($lc_endtime, $lc_rank, $lc_username)
    {
        $sql = "UPDATE `leonafx_accounts` SET timeleft = ?, rank = ? WHERE username = ?";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("sss", $lc_endtime, $lc_rank, $lc_username);
        $stmt->execute();
        return $stmt;
    }

    function getLocationInfoByIp()
    {
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = @$_SERVER['REMOTE_ADDR'];
        $result = array('country' => '', 'city' => '');
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        $ip_data = @json_decode(file_get_contents("https://ssl.geoplugin.net/json.gp?k=5520011b618a06ab&ip=" . $ip));
        if ($ip_data && $ip_data->geoplugin_countryName != null) {
            $result = $ip_data->geoplugin_countryName;
        }
        return $result;
    }

    function update_account3($lc_start, $lc_endtime, $lc_username)
    {
        $sql = "UPDATE `leonafx_accounts` SET startdate = ?, timeleft = ? WHERE username = ?";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("sss", $lc_start, $lc_endtime, $lc_username);
        $stmt->execute();
        return $stmt;
    }
}