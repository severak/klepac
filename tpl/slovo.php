<?=render('_header', ['title'=>$slovo['slovo']]);?>

<dl class="slovnik">

<dt><?=$slovo['slovo'];?></dt>

<dd>
<?php
if (!empty($slovo['vyslovnost'])) {
    echo '<small>['.$slovo['vyslovnost'].']</small><br>';
}
?>

<small><?=$druhy[$slovo['druh']]; ?></small>

<?=markdown($slovo['vyznam']); ?>

<?php if (!empty($slovo['etymologie'])) {
  echo '<h4>Etymologie:</h4>';
  echo markdown($slovo['etymologie']);
} ?>

<?php if (!empty($slovo['priklady'])) {
    echo '<h4>Příklady použití slova:</h4>';
    echo markdown($slovo['priklady']);
} ?>

<?php if (!empty($slovo['mluvci'])) {
    echo '<h4>Slovo používá:</h4>';
    echo markdown($slovo['mluvci']);
} ?>

<?php if (!empty($slovo['zdroje'])) {
    echo '<h4>Další zdroje:</h4>';
    echo markdown($slovo['zdroje']);
} ?>

<?php if (!empty($slovo['tagy'])) {
    echo '<br>';
    foreach (explode(' ', $slovo['tagy']) as $tag) {
        echo '<a href="/tagy/'.$tag.'">#'.$tag.'</a> ';
    }
} ?>


</dd>

</dl>

<hr/>
<p>Naposledy aktualizováno: <?=date('j.n.Y H:i', $slovo['aktualizovano'])?>
    <?php if (user()) {
        echo ' | <a href="/slova/upravit/'.$slovo['id'].'/">upravit heslo</a>';
    } ?>
</p>

<?=render('_footer');?>
