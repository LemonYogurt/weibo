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

function isLogin() {
	if (!$_COOKIE['userid'] || !$_COOKIE['username']) {
		return false;
	} else {
		return array('userid'=>$_COOKIE['userid'], 'username'=>$_COOKIE['username']);
	}
}

?>