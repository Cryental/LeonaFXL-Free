<?php
class license
{
    private $_db = "";
    function __construct($db)
    {
        $this->_db = $db;
    }

    function check_license($user_license)
    {
        $sql = "SELECT * FROM `leonafx_licenses` WHERE `license` = '$user_license'";
        $result = $this->_db->query($sql);
        return $result;
    }


    function insert_license($p_licensekey, $p_timeleft, $p_status, $p_lifetime, $p_timenow, $p_genuser)
    {
        $sql = "INSERT INTO `leonafx_licenses`(`license`, `timeleft`, `status`, `lifetime`, `generate_on`, `generated_by`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("ssssss", $p_licensekey, $p_timeleft, $p_status, $p_lifetime, $p_timenow, $p_genuser);
        $stmt->execute();
        return $stmt;
    }

    function delete_license($lc_licensekey)
    {
        $sql = "DELETE FROM leonafx_licenses WHERE license = ?";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("s", $lc_licensekey);
        $stmt->execute();
        return $stmt;
    }

    function update_license($lc_status, $lc_key)
    {
        $sql = "UPDATE `leonafx_licenses` SET status = ? WHERE license = ?";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("ss", $lc_status, $lc_key);;
        $stmt->execute();
        return $stmt;
    }
}