<?php
include_once('./_common.php');

include_once(G5_PATH.'/head.sub.php');

if ($is_guest) {
    $href = './login.php?'.$qstr.'&amp;url='.urlencode('./board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id);
    $href2 = str_replace('&amp;', '&', $href);
    echo <<<HEREDOC
    <script>
        alert('请您登录后使用此功能');
        opener.location.href = '$href2';
        window.close();
    </script>
    <noscript>
    <p>请您登录后使用此功能</p>
    <a href="$href">登录会员</a>
    </noscript>
HEREDOC;
    exit;
}

echo <<<HEREDOC
<script>
    if (window.name != 'win_scrap') {
        alert('系统错误！请您重新登录');
        window.close();
    }
</script>
HEREDOC;

if ($write['wr_is_comment'])
    alert_close('不能收藏评论及回帖内容');

$sql = " select count(*) as cnt from {$g5['scrap_table']}
            where mb_id = '{$member['mb_id']}'
            and bo_table = '$bo_table'
            and wr_id = '$wr_id' ";
$row = sql_fetch($sql);
if ($row['cnt']) {
    echo <<<HEREDOC
    <script>
    if (confirm('您已收藏此内容\\n\\n点击确定查看收藏内容'))
        document.location.href = './scrap.php';
    else
        window.close();
    </script>
    <noscript>
    <p>您已收藏此内容</p>
    <a href="./scrap.php">查看收藏内容</a>
    <a href="./board.php?bo_table={$bo_table}&amp;wr_id=$wr_id">返回</a>
    </noscript>
HEREDOC;
    exit;
}

include_once($member_skin_path.'/scrap_popin.skin.php');

include_once(G5_PATH.'/tail.sub.php');
?>
