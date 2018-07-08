<?php
include_once('./_common.php');

@include_once($board_skin_path.'/good.head.skin.php');

// 可以使用javascript时
if($_POST['js'] == "on") {
    $error = $count = "";

    function print_result($error, $count)
    {
        echo '{ "error": "' . $error . '", "count": "' . $count . '" }';
        if($error)
            exit;
    }

    if (!$is_member)
    {
        $error = '此功能需要登录会员后才能使用';
        print_result($error, $count);
    }

    if (!($bo_table && $wr_id)) {
        $error = '参数传递错误';
        print_result($error, $count);
    }

    $ss_name = 'ss_view_'.$bo_table.'_'.$wr_id;
    if (!get_session($ss_name)) {
        $error = '您只能向主题内容进行推荐或反对';
        print_result($error, $count);
    }

    $row = sql_fetch(" select count(*) as cnt from {$g5['write_prefix']}{$bo_table} ", FALSE);
    if (!$row['cnt']) {
        $error = '您访问的论坛版块不存在！';
        print_result($error, $count);
    }

    if ($good == 'good' || $good == 'nogood')
    {
        if($write['mb_id'] == $member['mb_id']) {
            $error = '您不能对自己发表的内容进行推荐或反对';
            print_result($error, $count);
        }

        if (!$board['bo_use_good'] && $good == 'good') {
            $error = '此栏目未开启推荐功能';
            print_result($error, $count);
        }

        if (!$board['bo_use_nogood'] && $good == 'nogood') {
            $error = '此栏目未开启反对功能';
            print_result($error, $count);
        }

        $sql = " select bg_flag from {$g5['board_good_table']}
                    where bo_table = '{$bo_table}'
                    and wr_id = '{$wr_id}'
                    and mb_id = '{$member['mb_id']}'
                    and bg_flag in ('good', 'nogood') ";
        $row = sql_fetch($sql);
        if ($row['bg_flag'])
        {
            if ($row['bg_flag'] == 'good')
                $status = '推荐';
            else
                $status = '反对';

            $error = "您已对文章进行$status";
            print_result($error, $count);
        }
        else
        {
            // 增加反对或推荐计数器
            sql_query(" update {$g5['write_prefix']}{$bo_table} set wr_{$good} = wr_{$good} + 1 where wr_id = '{$wr_id}' ");
            // 详情创建
            sql_query(" insert {$g5['board_good_table']} set bo_table = '{$bo_table}', wr_id = '{$wr_id}', mb_id = '{$member['mb_id']}', bg_flag = '{$good}', bg_datetime = '".G5_TIME_YMDHIS."' ");

            $sql = " select wr_{$good} as count from {$g5['write_prefix']}{$bo_table} where wr_id = '$wr_id' ";
            $row = sql_fetch($sql);

            $count = $row['count'];

            print_result($error, $count);
        }
    }
} else {
    include_once(G5_PATH.'/head.sub.php');

    if (!$is_member)
    {
        $href = './login.php?'.$qstr.'&amp;url='.urlencode('./board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id);

        alert('此功能需要登录会员后才能使用', $href);
    }

    if (!($bo_table && $wr_id))
        alert('参数传递错误');

    $ss_name = 'ss_view_'.$bo_table.'_'.$wr_id;
    if (!get_session($ss_name))
        alert('您只能向主题内容进行推荐或反对');

    $row = sql_fetch(" select count(*) as cnt from {$g5['write_prefix']}{$bo_table} ", FALSE);
    if (!$row['cnt'])
        alert('您访问的论坛版块不存在！');

    if ($good == 'good' || $good == 'nogood')
    {
        if($write['mb_id'] == $member['mb_id'])
            alert('您不能对自己发表的内容进行推荐或反对');

        if (!$board['bo_use_good'] && $good == 'good')
            alert('此栏目未开启推荐功能');

        if (!$board['bo_use_nogood'] && $good == 'nogood')
            alert('此栏目未开启反对功能');

        $sql = " select bg_flag from {$g5['board_good_table']}
                    where bo_table = '{$bo_table}'
                    and wr_id = '{$wr_id}'
                    and mb_id = '{$member['mb_id']}'
                    and bg_flag in ('good', 'nogood') ";
        $row = sql_fetch($sql);
        if ($row['bg_flag'])
        {
            if ($row['bg_flag'] == 'good')
                $status = '推荐';
            else
                $status = '反对';

            alert("您已对文章进行$status");
        }
        else
        {
            // 增加反对或推荐计数器
            sql_query(" update {$g5['write_prefix']}{$bo_table} set wr_{$good} = wr_{$good} + 1 where wr_id = '{$wr_id}' ");
            // 详情创建
            sql_query(" insert {$g5['board_good_table']} set bo_table = '{$bo_table}', wr_id = '{$wr_id}', mb_id = '{$member['mb_id']}', bg_flag = '{$good}', bg_datetime = '".G5_TIME_YMDHIS."' ");

            if ($good == 'good')
                $status = '推荐';
            else
                $status = '反对';

            $href = './board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id;

            alert("完成$status操作", '', false);
        }
    }
}

@include_once($board_skin_path.'/good.tail.skin.php');
?>