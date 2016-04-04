<?php
/*
	注册用户

    set user:userid:1: username zhangsan
    set user:userid:1:password 111111

    set user:username:zhangsan:userid 1

    userid生成
    incr global:userid，这样就达到了和mysql中的increment一致的效果了。

    具体的步骤：
    0：接收$_post参数，判断合法性，用户名和密码是否完全
    1：连接redis，查询该用户名，判断是否存在
    2：如果不存在，把用户名和密码写入redis
    3：完成登录操作
*/
include('./lib.php');
include('./header.php');

if (isLogin() != false) {
	header('location: home.php');
	exit;
}

$username = P('username');
$password = P('password');
$password2 = P('password2');
if (!$username || !$password || !$password2) {
    error('请输入完整注册信息');
}

// 判断密码是否一致
if ($password !== $password2) {
    error('2次密码不一样');
}

// 连接redis
$r = connredis();
// 查询用户名是否已被注册
// 把查询的结果打印出来

// 返回的是bool(false)
// var_dump是一个函数，会向页面输入内容的
// var_dump($r->get('user:username'.$username.':userid'));

if ($r->get('user:username'.$username.':userid')) {
    error('用户名已被注册，请更换');
}

// 获取userid
$userid = $r->incr('global:userid');
$r->set('user:userid:'.$userid.':username', $username);
$r->set('user:userid:'.$userid.':password', $password);
$r->set('user:username:'.$username.':userid', $userid);

// 通过一个链表，维护50个最新的userid
// 有新用户注册的时候，让它push在最左端
$r->lpush('newuserlink', $userid);
// 剪切链表，左侧从0开始，右侧从-1开始
$r->ltrim('newuserlink', 0, 49);

// lrange newuserlink 0 -1查看链表全部内容
// redis 的sort功能很强大，可以类似与mysql的左连接功能

include('footer.php');
?>

