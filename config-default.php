<?php

$_config=array();
//数据库配置
$_config['db']['hostname']='';
$_config['db']['username']='';
$_config['db']['password']='';
$_config['db']['database']='';
$_config['db']['charset']='utf8';
$_config['db']['pconnect']=0;
$_config['db']['log']=1;
$_config['db']['logpath']='';

//时区设置
date_default_timezone_set('Asia/Shanghai');

//设置引用目录
ini_set('include_path','.');
