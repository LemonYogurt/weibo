把最新注册的用户取出来，把最新的50条微博取出来
新浪微博的粉丝关注关系，用了一个很不错的开源的软件，很不错的算法来存储，是200个G的内存
微博的相互关注关系就存储完了。



<?php

include('./lib.php');
include('./header.php');

if (!isLogin()) {
	header('location: index.php');
	exit;
}
// 空数组
$newuserlist = array();
$r = connredis();
// 声明几个条件，array()存储的就是排序规则
$newuserlist = $r->sort('newuserlink', array('sort'=>'desc', 'get'=>'user:userid:*:username'));
// 完成的功能是，首先进行倒序排序，然后将取出的userid，分别替换*，然后取出username，相当于mysql中的左连接了。

//print_r($newuserlist);
// 返回的结果是：Array([0]=>yanshiba [1]=>test1 [2]=>test2)
//exit;

?>
<div id="navbar">
<a href="index.php">主页</a>
| <a href="timeline.php">热点</a>
| <a href="logout.php">退出</a>
</div>
</div>
<h2>热点</h2>

<i>最新注册用户(redis中的sort用法)</i><br>
<div>

<?php foreach($newuserlist as $u) {?>

<a class="username" href="profile.php?u=<?php echo $u' ?>"><?php echo $u' ?></a> </div>
<?php } ?>

<br><i>最新的50条微博!</i><br>
<div class="post">
<a class="username" href="profile.php?u=test">test</a>
world<br>
<i>22 分钟前 通过 web发布</i>
</div>

<div class="post">
<a class="username" href="profile.php?u=test">test</a>
hello<br>
<i>22 分钟前 通过 web发布</i>
</div>

<? php include('./footer.php');?>
