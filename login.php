<?php
include('./lib.php');
include('./header.php');

/*
登陆页面：
0：接收$_POST，判断完全性
1：查询用户名是否存在
2：如果用户名存在，查询密码是否匹配
3：登录成功后，设置cookie
*/

if (isLogin() != false) {
	header('location: home.php');
	exit;
}

$username = P('username');
$password = P('password');

if (!$username || !$password) {
	error('请输入完整');
}

$r = connredis();
$userid = $r->get('user:username:'.$username.':userid');

if (!$userid) {
	error('用户名不存在');
}

$realpass = $r->get('user:userid:'.$userid.':password');
if ($password !== $realpass) {
	error('密码不对');
}

// 设置cookie，登录成功

// 这个函数是在lib.php中定义的
$authsecret = randsecret();
// 我们还要进行验证，所以，把字符串放在redis里面，
$r->set('user:userid:'.$userid.':authsecret', $authsecret);

setcookie('username', $username);
setcookie('userid', $userid);
setcookie('authsecret', $authsecret);

// 设置完成后，将页面转到home.php
header('location: home.php');
include('footer.php');
?>

