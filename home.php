<?php
include('./lib.php');
include('./header.php');
if (($user = isLogin()) == false) {
    header('location: index.php');
    exit;
}

$r = connredis();
// 取出自己发的和粉主推过来的信息
$r->ltrim('receivepost:'.$user['userid'], 0, 49);
// 并不是想要别人推送给我的id，而是我想要它的内容
// 它循环的取出微博的id，然后循环的把id替换到*，再取出它的内容
$newpost = $r->sort('receiveport:'.$user['userid'], array('sort'=>'desc', 'get'=>'post:postid:*:content'));

// 计算几个粉丝，几个关注
// 就是计算集合的元素个数
// 专门用来计算集合的个数
$myfans = $r->sCard('following:'.$user['userid']);
$mystar = $r->sCard('follower:'.$user['userid']);

?>

<div id="navbar">
<a href="index.php">主页</a>
| <a href="timeline.php">热点</a>
| <a href="logout.php">退出</a>
</div>
</div>
<div id="postform">
<form method="POST" action="post.php">
<?php echo $user['username']; ?>, 有啥感想?
<br>
<table>
<tr><td><textarea cols="70" rows="3" name="status"></textarea></td></tr>
<tr><td align="right"><input type="submit" name="doit" value="Update"></td></tr>
</table>
</form>
<div id="homeinfobox">
<?php echo $myfans; ?> 粉丝<br>
<?php echo $mystar; ?> 关注<br>
</div>
</div>
这里得到的微博，不仅要得到我自己发布的微博，还要得到我关注人的微博
有两种方式：
把关注的人循环一遍，把它们的微博取出来
还有一种办法，就是当一个人发微博的时候，直接推送给我的粉丝

<?php foreach($newpost as $c) { ?>


<div class="post">
<a class="username" href="profile.php?u=test">test</a> <?php echo $c; }?><br>
<i>11 分钟前 通过 web发布</i>
</div>
<?php }?>
<?php include('./footer.php');?>