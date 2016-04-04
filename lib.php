<?php

// 这里是为了让它不报NOTICE提示
Error_reporting(E_ALL & ~E_NOTICE);

// 基础库，放置一些常用的函数
function P($key) {
	return $_POST[$key];
}

function G($key) {
	return $_GET[$key];
}

// error函数
function error($msg) {
	echo '<div>';
	echo $msg;
	echo '</div>';
	include('./footer.php');
	exit;
}

function connredis() {
	static $r = null;

	if ($r !== NULL) {
		return $r;
	}
	$r = new redis();
	$r->connect('localhost', 6379);

	return $r;
}

// 判断用户是否登录
// 还要判断cookie里面是否有authsecret，就算有，还要和redis服务器里面的进行匹配，这样才可以
//

function isLogin() {
	if (!$_COOKIE['userid'] || !$_COOKIE['username']) {
		return false;
	} else {
		return array('userid'=>$_COOKIE['userid'], 'username'=>$_COOKIE['username']);
	}

	if (!$_COOKIE['authsecret']) {
		return false;
	}

	$r = connredis();
	// 取出的这个authsecret必须和cookie里面的authsecret一致才可以
	$authsecret = $r->get('user:userid:'.$_COOKIE['userid'].':authsecret');

	if ($authsecret !== $_COOKIE['$authsecret']) {
		return false;
	}
}

// 为了便于操作，写一个生成随机数的函数
function randsecret() {
	$str = 'abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
	// 然后把这个字符串打乱
	return substr(str_shuffle($str), 0, 16);
}

// 格式化时间
function formattime($time) {
	// time()获取当前的时间错
	$sec = time() - $time;

	if ($sec >= 86400) {
		return floor($sec / 86400).'天';
	} else if ($sec >= 3600) {
		return floor($sec / 3600).'小时';
	} else if ($sec >= 60) {
		return floor($sec / 60).'分钟';
	} else {
		return $sec.'秒';
	}
}

?>