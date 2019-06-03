<?php
class log
{
    private $_db = "";
    function __construct($db)
    {
        $this->_db = $db;
    }

    function insert_log($log_data)
    {
        $randomString = '';
        for ($i = 0; $i < 8; $i++) {
            $randomString .= "0123456789"[rand(0, strlen("0123456789") - 1)];
        }
        $log_data['log_ip'] = $_SERVER['REMOTE_ADDR'];
        $log_data['log_status'] = "DENY";
        $sql = "INSERT INTO `leonafx_logs`(`log_id`, `log_date`, `log_ip`, `log_username`, `log_hwid`, `log_summary`, `log_status`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("sssssss", $randomString, $log_data['log_date'], $log_data['log_ip'], $log_data['log_username'], $log_data['log_hwid'], $log_data['log_summary'], $log_data['log_status']);
        $stmt->execute();
        return $stmt;
    }

    function insert_log_success($log_data)
    {
        $randomString = '';
        for ($i = 0; $i < 8; $i++) {
            $randomString .= "0123456789"[rand(0, strlen("0123456789") - 1)];
        }
        $log_data['log_ip'] = $_SERVER['REMOTE_ADDR'];
        $log_data['log_status'] = "GRANT";
        $sql = "INSERT INTO `leonafx_logs`(`log_id`, `log_date`, `log_ip`, `log_username`, `log_hwid`, `log_summary`, `log_status`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("sssssss", $randomString, $log_data['log_date'], $log_data['log_ip'], $log_data['log_username'], $log_data['log_hwid'], $log_data['log_summary'], $log_data['log_status']);
        $stmt->execute();
        return $stmt;
    }
}