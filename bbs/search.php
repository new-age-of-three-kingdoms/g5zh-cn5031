<?php
include_once('./_common.php');

$g5['title'] = '全站搜索结果';
include_once('./_head.php');

$search_table = Array();
$table_index = 0;
$write_pages = "";
$text_stx = "";
$srows = 0;

$stx = strip_tags($stx);
//$stx = preg_replace('/[[:punct:]]/u', '', $stx); // 删除特殊字符
$stx = get_search_string($stx); // 删除特殊字符
if ($stx) {
    $stx = preg_replace('/\//', '\/', trim($stx));
    $sop = strtolower($sop);
    if (!$sop || !($sop == 'and' || $sop == 'or')) $sop = 'and'; // 关联and，or
    $srows = isset($_GET['srows']) ? $_GET['srows'] : 10;
    if (!$srows) $srows = 10; // 每页搜索行数量

    $g5_search['tables'] = Array();
    $g5_search['read_level'] = Array();
    $sql = " select gr_id, bo_table, bo_read_level from {$g5['board_table']} where bo_use_search = 1 and bo_list_level <= '{$member['mb_level']}' ";
    if ($gr_id)
        $sql .= " and gr_id = '{$gr_id}' ";
    $onetable = isset($onetable) ? $onetable : "";
    if ($onetable) // 如果仅搜索一个论坛版块时
        $sql .= " and bo_table = '{$onetable}' ";
    $sql .= " order by bo_order, gr_id, bo_table ";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        if ($is_admin != 'super')
        {
            // 如有群组访问权限设定则终止搜索
            $sql2 = " select gr_use_access, gr_admin from {$g5['group_table']} where gr_id = '{$row['gr_id']}' ";
            $row2 = sql_fetch($sql2);
            // 使用群组访问权限时
            if ($row2['gr_use_access']) {
                // 如果有群组管理员且当前会员就是管理员时通过
                if ($row2['gr_admin'] && $row2['gr_admin'] == $member['mb_id']) {
                    ;
                } else {
                    $sql3 = " select count(*) as cnt from {$g5['group_member_table']} where gr_id = '{$row['gr_id']}' and mb_id = '{$member['mb_id']}' and mb_id <> '' ";
                    $row3 = sql_fetch($sql3);
                    if (!$row3['cnt'])
                        continue;
                }
            }
        }
        $g5_search['tables'][] = $row['bo_table'];
        $g5_search['read_level'][] = $row['bo_read_level'];
    }

    $search_query = 'sfl='.urlencode($sfl).'&amp;stx='.urlencode($stx).'&amp;sop='.$sop;


    $text_stx = get_text(stripslashes($stx));

    $op1 = '';

    // 使用空格符分隔搜索字段
    $s = explode(' ', strip_tags($stx));

    // 使用（+）分隔搜索字段
    $field = explode('||', trim($sfl));

    $str = '(';
    for ($i=0; $i<count($s); $i++) {
        if (trim($s[$i]) == '') continue;

        $search_str = $s[$i];

        // 热门关键词
        insert_popular($field, $search_str);

        $str .= $op1;
        $str .= "(";

        $op2 = '';
        // 根据字段实现多字段搜索
        for ($k=0; $k<count($field); $k++) {
            $str .= $op2;
            switch ($field[$k]) {
                case 'mb_id' :
                case 'wr_name' :
                    $str .= "$field[$k] = '$s[$i]'";
                    break;
                case 'wr_subject' :
                case 'wr_content' :
                    if (preg_match("/[a-zA-Z]/", $search_str))
                        $str .= "INSTR(LOWER({$field[$k]}), LOWER('{$search_str}'))";
                    else
                        $str .= "INSTR({$field[$k]}, '{$search_str}')";
                    break;
                default :
                    $str .= "1=0"; // 始终为假值
                    break;
            }
            $op2 = " or ";
        }
        $str .= ")";

        $op1 = " {$sop} ";
    }
    $str .= ")";

    $sql_search = $str;

    $str_board_list = "";
    $board_count = 0;

    $time1 = get_microtime();

    $total_count = 0;
    for ($i=0; $i<count($g5_search['tables']); $i++) {
        $tmp_write_table   = $g5['write_prefix'] . $g5_search['tables'][$i];

        $sql = " select wr_id from {$tmp_write_table} where {$sql_search} ";
        $result = sql_query($sql, false);
        $row['cnt'] = @mysql_num_rows($result);

        $total_count += $row['cnt'];
        if ($row['cnt']) {
            $board_count++;
            $search_table[] = $g5_search['tables'][$i];
            $read_level[]   = $g5_search['read_level'][$i];
            $search_table_count[] = $total_count;

            $sql2 = " select bo_subject from {$g5['board_table']} where bo_table = '{$g5_search['tables'][$i]}' ";
            $row2 = sql_fetch($sql2);
            $sch_class = "";
            $sch_all = "";
            if ($onetable == $g5_search['tables'][$i]) $sch_class = "class=sch_on";
            else $sch_all = "class=sch_on";
            $str_board_list .= '<li><a href="'.$_SERVER['PHP_SELF'].'?'.$search_query.'&amp;gr_id='.$gr_id.'&amp;onetable='.$g5_search['tables'][$i].'" '.$sch_class.'><strong>'.$row2['bo_subject'].'</strong><span class="cnt_cmt">'.$row['cnt'].'</span></a></li>';
        }
    }

    $rows = $srows;
    $total_page = ceil($total_count / $rows);  // 计算所有页码
    if ($page < 1) { $page = 1; } // 如果没有页码时设置为1
    $from_record = ($page - 1) * $rows; // 获取开始行

    for ($i=0; $i<count($search_table); $i++) {
        if ($from_record < $search_table_count[$i]) {
            $table_index = $i;
            $from_record = $from_record - $search_table_count[$i-1];
            break;
        }
    }

    $bo_subject = array();
    $list = array();

    $k=0;
    for ($idx=$table_index; $idx<count($search_table); $idx++) {
        $sql = " select bo_subject from {$g5['board_table']} where bo_table = '{$search_table[$idx]}' ";
        $row = sql_fetch($sql);
        $bo_subject[$idx] = $row['bo_subject'];

        $tmp_write_table = $g5['write_prefix'] . $search_table[$idx];

        $sql = " select * from {$tmp_write_table} where {$sql_search} order by wr_id desc limit {$from_record}, {$rows} ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            // 如果关键词也增加超链接会导致增加负荷
            $list[$idx][$i] = $row;
            $list[$idx][$i]['href'] = './board.php?bo_table='.$search_table[$idx].'&amp;wr_id='.$row['wr_parent'];

            if ($row['wr_is_comment'])
            {
                $sql2 = " select wr_subject, wr_option from {$tmp_write_table} where wr_id = '{$row['wr_parent']}' ";
                $row2 = sql_fetch($sql2);
                //$row['wr_subject'] = $row2['wr_subject'];
                $row['wr_subject'] = get_text($row2['wr_subject']);
            }

            // 不搜索加密贴
            if (strstr($row['wr_option'].$row2['wr_option'], 'secret'))
                $row['wr_content'] = '[使用加密功能的主题内容]';

            $subject = get_text($row['wr_subject']);
            if (strstr($sfl, 'wr_subject'))
                $subject = search_font($stx, $subject);

            if ($read_level[$idx] <= $member['mb_level'])
            {
                //$content = cut_str(get_text(strip_tags($row['wr_content'])), 300, "…");
                $content = strip_tags($row['wr_content']);
                $content = get_text($content, 1);
                $content = strip_tags($content);
                $content = str_replace('&nbsp;', '', $content);
                $content = cut_str($content, 300, "…");

                if (strstr($sfl, 'wr_content'))
                    $content = search_font($stx, $content);
            }
            else
                $content = '';

            $list[$idx][$i]['subject'] = $subject;
            $list[$idx][$i]['content'] = $content;
            $list[$idx][$i]['name'] = get_sideview($row['mb_id'], get_text(cut_str($row['wr_name'], $config['cf_cut_name'])), $row['wr_email'], $row['wr_homepage']);

            $k++;
            if ($k >= $rows)
                break;
        }
        sql_free_result($result);

        if ($k >= $rows)
            break;

        $from_record = 0;
    }

    $write_pages = get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['PHP_SELF'].'?'.$search_query.'&amp;gr_id='.$gr_id.'&amp;srows='.$srows.'&amp;onetable='.$onetable.'&amp;page=');
}

$group_select = '<label for="gr_id" class="sound_only">论坛 群组选择</label><select name="gr_id" id="gr_id" class="select"><option value="">全部 分类';
$sql = " select gr_id, gr_subject from {$g5['group_table']} order by gr_id ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
    $group_select .= "<option value=\"".$row['gr_id']."\"".get_selected($_GET['gr_id'], $row['gr_id']).">".$row['gr_subject']."</option>";
$group_select .= '</select>';

if (!$sfl) $sfl = 'wr_subject';
if (!$sop) $sop = 'or';

include_once($search_skin_path.'/search.skin.php');

include_once('./_tail.php');
?>
