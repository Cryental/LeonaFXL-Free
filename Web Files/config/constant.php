<?php
#Database Information
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "leonafx_licensing");

#Security Values
define("TIMEOUT_TIMESTAMP", "5");
define("COMMUNICATION_KEY", "jRu44GFdyhFPCPq9C2Wf");
define("E_METHOD", "aes-256-cbc");
define("E_ENKEY", "u47h5k48wftutj2sg5cc2cswyd3wae28");
define("E_ENIV", "5w6qt6znny73chdq8cwqu8uhng2hkmmy");
define("E_PASSWORD", "46192DAE7F229");
define("E_RNDM_STR", "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
define("E_IV", chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0));

#Log Write Values
define("LOGIN_FIELDEMPTY", "Login Process - Fields Empty");
define("LOGIN_INCORRECT", "Login Process - Incorrect Username or Password");
define("LOGIN_BANNED", "Login Process - Banned User");
define("LOGIN_HARDWAREIDERROR", "Login Process - Hardware ID Match Error");
define("LOGIN_LICENSEEMPTY", "Login Process - License Empty");
define("LOGIN_GRANTED_LIFETIME", "Login Process - Success + Granted to Lifetime User");
define("LOGIN_GRANTED_ADMIN", "Login Process - Success + Granted to Admin User");
define("LOGIN_GRANTED_USER", "Login Process - Success + Granted to Normal User");
define("LOGIN_LICENSEEXPIRED", "Login Process - Success + License Expired Returned");

define("REGISTER_FIELDEMPTY", "Registration Process - Fields Empty");
define("REGISTER_USERNAMELENGTH_ERROR", "Registration Process - Username Length or Alphabet Error");
define("REGISTER_EMAILFORMAT_ERROR", "Registration Process - E-mail Format Error");
define("REGISTER_PASSWORDS_MATCHERROR", "Registration Process - Password Match Error");
define("REGISTER_EXISTUSERNAME", "Registration Process - Exist User Name");
define("REGISTER_BANNED", "Registration Process - Banned User");
define("REGISTER_SUCCESSED", "Registration Process - Successfully Registered");

define("FORGOTPWD_FIELDEMPTY", "Forgot Password Process - Field Empty");
define("FORGOTPWD_PASSWORDS_MATCHERROR", "Forgot Password Process - Password Match Error");
define("FORGOTPWD_ANSWERERROR", "Forgot Password Process - Secret Question Match Error");
define("FORGOTPWD_EMAILINCORRECT", "Forgot Password Process - Email Match Error");
define("FORGOTPWD_SUCCESSED", "Forgot Password Process - Successfully Reset Password");
define("FORGOTPWD_INCORRECT", "Forgot Password Process - Incorrect Username or Password");

define("CHANGEPWD_FIELDEMPTY", "Change Password Process - Field Empty");
define("CHANGEPWD_INCORRECT", "Change Password Process - Incorrect Username or Password");
define("CHANGEPWD_HARDWAREIDERROR", "Change Password Process - Hardware ID Match Error");
define("CHANGEPWD_PASSWORDS_MATCHERROR", "Change Password Process - Password Match Error");
define("CHANGEPWD_SUCCESSED", "Change Password Process - Successfully Changed Password");
define("CHANGEPWD_CANTFIND", "Change Password Process - Incorrect Username or Password");

define("AUTO_BANNED", "Banned By Auto System");

define("LICENSEVAILD_FIELDEMPTY", "License Validation Process - Field Empty");
define("LICENSEVAILD_INCORRECT", "License Validation Process - Incorrect Username or Password");
define("LICENSEVAILD_HARDWAREIDERROR", "License Validation Process - Hardware ID Match Error");
define("LICENSEVAILD_USEDLICENSE", "License Validation Process - Already Used License");
define("LICENSEVAILD_BANNED", "License Validation Process - Banned License");
define("LICENSEVAILD_LIFETIMEALEADY", "License Validation Process - Lifetime Validated Already");
define("LICENSEVAILD_ACTIVATED", "License Validation Process - Successfully Activated");
define("LICENSEVAILD_CANTVAILD", "License Validation Process - Can't Valid License");
define("LICENSEVAILD_CANTFIND", "License Validation Process - Can't Find License");
define("LICENSEVAILD_UPDATE_ACCOUNT", "License Validation Process - Updated User Information By License Validation Process");

define("LICENSEREG_FIELDEMPTY", "License Registration Process - Field Empty");
define("LICENSEREG_INCORRECT", "License Registration Process - Incorrect Username or Password");
define("LICENSEREG_HARDWAREIDERROR", "License Registration Process - Hardware ID Match Error");
define("LICENSEREG_TIMELEFTERROR", "License Registration Process - TimeLeft Parameter Error");
define("LICENSEREG_NOTADMIN", "License Registration Process - Processed By Non-Admin");
define("LICENSEREG_LIFETIMEERROR", "License Registration Process - Lifetime Parameter Error");
define("LICENSEREG_CREATED", "License Registration Process - Successfully Created License");

define("INVALID_ACCESS", "Access Denied - Invalid Communication Key");
define("TIMESTAMP_ERROR", "Access Denied - Invalid Timestamp");
