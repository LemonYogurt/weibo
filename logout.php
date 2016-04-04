<? php
	include ('./lib.php');

	$userid = $_COOKIE['userid'];
	setcookie('username', '', -1);
	setcookie('userid', '', -1);
	setcookie('authsecret', '', -1);

	// 退出的时候不仅要消除cookie，还要消除加密密码，这样才能完成
	$r = connredis();
	$r->set('user:userid:'.$userid.':authsecret', '');

	header('location: index.php');
?>