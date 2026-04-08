<?=render('_header', ['title'=>'O projektu']);?>

<?php echo '<div class="markdown">' . markdown($slovo['vyznam']) . '</div>'; ?>

<?=render('_footer');?>
