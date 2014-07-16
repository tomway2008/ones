<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getPwd($source) {
    return md5($source);
}
//用户缓存
function getUserCache() {
    $userData = F("User/All");
    if (!$userData) {
        $tmp = D("User")->select();
        foreach ($tmp as $v) {
            unset($v["password"]);
            $userData[$v["id"]] = $v;
        }
        F("User/All", $userData);
    }
    return $userData;
}

function getUserTruenameArray() {
    $users = getUserCache();
    foreach($users as $k=>$v) {
        $users[$k] = $v["truename"];
    }
    return $users;
}

function toDate($str, $format = "Y-m-d H:i:s") {
    return date($format, $str);
}
//根据UID返回用户真实姓名
function toTruename($uid) {
    if (!$uid) {
        return;
    }
    $userData = getUserCache();
    return $userData[$uid]["truename"];
}

//根据UID返回用户名
function toUsername($uid) {
    if (!$uid) {
        return L("Unnamed");
    }
    $userData = getUserCache();
    return $userData[$uid]["username"];
}

//数据库字段配置缓存
function makeDBConfigCache() {
    $config = F("Config/DB");
    if (!$config) {
        $model = D("Config");
        $tmp = $model->select();
        foreach ($tmp as $v) {
            $config[$v["alias"]] = $v["value"];
        }
    }
    return $config;
}
//字段配置调用
function DBC($name="") {
    $config = makeDBConfigCache();
    return $name ? $config[$name] : $config;
}

/**
 * 去除数组索引
 */
function reIndex($data) {
    foreach ($data as $v) {
        $tmp[] = $v;
    }
    return $tmp;
}

/**
 * 清除缓存
 */
function clearCache($type = 0, $path = NULL) {
    if (is_null($path)) {
        switch ($type) {
            case 0:// 模版缓存目录
                $path = CACHE_PATH;
                break;
            case 1:// 数据缓存目录
                $path = TEMP_PATH;
                break;
            case 2:// 日志目录
                $path = LOG_PATH;
                break;
            case 3:// 数据目录
                $path = DATA_PATH;
                break;
        }
    }
//    import("ORG.Io.Dir");
    delDirAndFile($path);
}
function delDirAndFile($dirName) {
    if ($handle = opendir($dirName)) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..") {
                if (is_dir($dirName."/".$item)) {
                    delDirAndFile($dirName."/".$item);
                } else {
                    unlink($dirName."/".$item) or die("Can't delete". $dirName."/".$item);
                }
            }
        }
        @ closedir($handle);
        @ rmdir($dirName);
    }
}

/*
 * 生成日期序列
 * **/
function makeDateRange($start, $end, $step, $format = "m-d") {
    $tmp = range($start, $end, $step);
    foreach ($tmp as $v) {
        $dateRange[] = date($format, $v);
    }
    return $dateRange;
}

/*
 * 判断是否JSON
 * **/
function is_not_json($str){
    return is_null(json_decode($str));
}

/**
 * 
 */
function inExplodeArray($id, $ids, $split=",") {
    return in_array($id, explode($split, $ids));
}

function getCurrentUid() {
    return $_SESSION["user"]["id"];
}

/**
 * 汉字转拼音
 */
function getfirstchar($s0) {
    $fchar = ord($s0{0});
    if ($fchar >= ord("A") and $fchar <= ord("z"))
        return strtoupper($s0{0});
    $s1 = iconv("UTF-8", "gb2312", $s0);
    $s2 = iconv("gb2312", "UTF-8", $s1);
    if ($s2 == $s0) {
        $s = $s1;
    } else {
        $s = $s0;
    }
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 and $asc <= -20284)
        return "A";
    if ($asc >= -20283 and $asc <= -19776)
        return "B";
    if ($asc >= -19775 and $asc <= -19219)
        return "C";
    if ($asc >= -19218 and $asc <= -18711)
        return "D";
    if ($asc >= -18710 and $asc <= -18527)
        return "E";
    if ($asc >= -18526 and $asc <= -18240)
        return "F";
    if ($asc >= -18239 and $asc <= -17923)
        return "G";
    if ($asc >= -17922 and $asc <= -17418)
        return "H";
    if ($asc >= -17417 and $asc <= -16475)
        return "J";
    if ($asc >= -16474 and $asc <= -16213)
        return "K";
    if ($asc >= -16212 and $asc <= -15641)
        return "L";
    if ($asc >= -15640 and $asc <= -15166)
        return "M";
    if ($asc >= -15165 and $asc <= -14923)
        return "N";
    if ($asc >= -14922 and $asc <= -14915)
        return "O";
    if ($asc >= -14914 and $asc <= -14631)
        return "P";
    if ($asc >= -14630 and $asc <= -14150)
        return "Q";
    if ($asc >= -14149 and $asc <= -14091)
        return "R";
    if ($asc >= -14090 and $asc <= -13319)
        return "S";
    if ($asc >= -13318 and $asc <= -12839)
        return "T";
    if ($asc >= -12838 and $asc <= -12557)
        return "W";
    if ($asc >= -12556 and $asc <= -11848)
        return "X";
    if ($asc >= -11847 and $asc <= -11056)
        return "Y";
    if ($asc >= -11055 and $asc <= -10247)
        return "Z";
    return null;
}

function Pinyin($zh) {
    $ret = "";
    $s1 = iconv("UTF-8", "gb2312", $zh);
    $s2 = iconv("gb2312", "UTF-8", $s1);
    if ($s2 == $zh) {
        $zh = $s1;
    }
    for ($i = 0; $i < strlen($zh); $i++) {
        $s1 = substr($zh, $i, 1);
        $p = ord($s1);
        if ($p > 160) {
            $s2 = substr($zh, $i++, 2);
            $ret .= getfirstchar($s2);
        } else {
            $ret .= $s1;
        }
    }
    return $ret;
}

/**
 * 内置模块是否已启用
 */
function isModuleEnabled($moduleName) {
    $modules = F("loadedApp");
    return in_array($moduleName, $modules);
}
function isAppLoaded($appName) {
    return isModuleEnabled($appName);
}

/**
 * 生成原厂编码
 */
function makeFactoryCode($data, $factoryCode=null) {
    $format = C("FactoryCodeFormat");
    if($factoryCode) {
        $data["factory_code"] = $factoryCode;
    }
    foreach($format as $v) {
        if(!array_key_exists($v, $data)) {
            return false;
        }
        $result[] = $data[$v];
    }
    
    return implode(C("FactoryCodeSplit"), $result);
    
}

/**
 * 检查数组数据是否完整
 */
function checkParamsFull($data, $needed) {
    foreach($needed as $v) {
        if(!array_key_exists($v, $data)) {
            return false;
        }
    }
    return true;
}

/**
 * 发送邮件
 */
function sendMail($subject, $to, $toName, $content, $attach=array()) {
    import("@.ORG.phpmailer.Mail");
    $mail = new PHPMailer(); //实例化 
    $mail->IsSMTP(); // 启用SMTP 
    $mail->Host = C("MAIL_SMTP"); //SMTP服务器 以163邮箱为例子 
    $mail->Port = C("MAIL_SMTP_PORT") ? C("MAIL_SMTP_PORT") : 25;  //邮件发送端口 
    $mail->SMTPAuth   = true;  //启用SMTP认证 

    $mail->CharSet  = C("MAIL_CHARSET"); //字符集 
    $mail->Encoding = C("MAIL_ENCODE") ? C("MAIL_ENCODE") : "base64"; //编码方式 

    $mail->Username = C("MAIL_LOGINNAME");  //你的邮箱 
    $mail->Password = C("MAIL_PASSWORD");  //你的密码 
    $mail->Subject = $subject; //邮件标题 

    $mail->From = C("MAIL_ADDRESS");  //发件人地址（也就是你的邮箱） 
    $mail->FromName = C("MAIL_FORM");  //发件人姓名 

    $mail->AddAddress($to, $toName);//添加收件人（地址，昵称） 
    
    if($attach) {
        foreach($attach as $a) {
            $rs = $mail->AddAttachment($a); // 添加附件,并指定名称 
            if(!$rs) {
                return $mail->ErrorInfo;
            }
        }
    }
    $mail->IsHTML(true); //支持html格式内容 
    $mail->Body = $content;
    if(!$mail->Send()) { 
        return $mail->ErrorInfo; 
    } else { 
        return true;
    } 
    
}

/**
 * 数据库备份
 * @require sendMail
 * @require php_zip
 * @require mysql_dump
 */
function DBBackup($options=array()) {
    set_time_limit(60);
    $savename = $savename ? $savename : sprintf("%s/Data/Backup/DB_Backup_%s.sql", ENTRY_PATH, date("YmdHis", CTS));
    $command = sprintf("mysqldump %s -u %s -p%s > %s", 
                    C("DB_NAME"),
                    C("DB_USER"),
                    C("DB_PWD"),
                    $savename
                );
    exec($command);
    if(in_array("zip", $options)) {
        sleep(2);
        $zip = new ZipArchive;
        $zipname = str_replace(".sql", ".zip", $savename);
        if ($zip->open($zipname, ZIPARCHIVE::CREATE) === true) {
            $zip->addFile($savename, basename($savename));
            $zip->close();
        } else {
            return false;
        }
    }
    
    if(in_array("sendmail", $options)) {
        sleep(2);
        $file = in_array("zip", $options) ? $zipname : $savename;
        $rs = sendMail("ONES DBBackup", DBC("backup.sendto.email"), "ONES Customer", "ONES Data Backup", array($file));
        if(true !== $rs) {
            return $rs;
        }
    }
    if(in_array("autodelete", $options)) {
        @unlink($savename);
        @unlink($zipname);
    }
    return true;
//        $rs = sendMail("hello", "335454250@qq.com", "老大", '123123', array(
//            "/Users/nemo/wwwroot/newopenx/erp_server/1.zip"
//        ));
//        var_dump($rs);
////        echo 123;exit; 
}

/**
 * 生成单据编号
 */
function makeBillCode($prefix=""){
    return sprintf("%s%s%d", $prefix, date("ymdHis"), rand(0,9));
}

/*
 * 目录复制
 * **/
function recursionCopy($src,$dst) {  // 原目录，复制到的目录
    $dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ) {

        if ($file != '.' && $file != '..' && $file != "__MACOSX") {
            if ( is_dir($src . '/' . $file) ) {
                if(!is_dir($dst . '/' . $file)) {
                    mkdir($dst . '/' . $file, 0777);
                }
                recursionCopy($src . '/' . $file, $dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

/**
 * 统一处理的类型
 * **/
function getTypes($type) {
    $types = F("Types");
    if(!$types) {
        $tmp = D("Types")->order("listorder DESC,id ASC")->select();
        foreach($tmp as $v) {
            $types[$v["type"]][] = $v;
        }
        F("Types", $types);
    }
    return $types[$type];
}

function getTypesIndex($type, $idField="id", $field="name") {
    $types = getTypes($type);
    foreach($types as $k=>$v) {
        $tmp[$v[$idField]] = $v[$field];
    }
    return $tmp;
}
function getTypeByAlias($type, $alias) {
    $types = getTypes($type);
    foreach($types as $t) {
        if($t["alias"] == $alias) {
            return $t;
        }
    }
}
function getTypeIdByAlias($type, $alias) {
    $types = getTypes($type);
    foreach($types as $t) {
        if($t["alias"] == $alias) {
            return $t["id"];
        }
    }
}