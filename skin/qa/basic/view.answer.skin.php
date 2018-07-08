<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页
?>

<section id="bo_v_ans">
    <h2>回复: <?php echo get_text($answer['qa_subject']); ?></h2>
    <a href="<?php echo $rewrite_href; ?>" class="btn_b01">追加提问</a>

    <div id="ans_datetime">
        <?php echo $answer['qa_datetime']; ?>
    </div>

    <div id="ans_con">
        <?php echo conv_content($answer['qa_content'], $answer['qa_html']); ?>
    </div>

    <div id="ans_add">
        <?php if($answer_update_href) { ?>
        <a href="<?php echo $answer_update_href; ?>" class="btn_b01">编辑回复</a>
        <?php } ?>
        <?php if($answer_delete_href) { ?>
        <a href="<?php echo $answer_delete_href; ?>" class="btn_b01" onclick="del(this.href); return false;">删除回复</a>
        <?php } ?>
    </div>
</section>