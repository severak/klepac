<?= render('_header', ['title' => $tema ?? false]) ?>

<dl class="slovnik">
    <?php
    $druhy = [
      1 => 'pod. jm.',
      2 => 'příd. jm.',
      3 => 'zájm.',
      4 => 'čís.',
      5 => 'slov.',
      6 => 'přís.',
      7 => 'předl.',
      8 => 'spoj.',
      9 => 'část.',
      10 => 'cit.',
      11 => 'univ. sl.',
      12 => 'zkr.',
      99 => 'ost.'
    ];

    if (isset($tema)) {
        echo '<h3>Téma: '.$tema.'</h3>';
    }

    foreach ($slova as $slovo) {
        echo '<dt><a href="/slovo/'.urlencode($slovo['slovo']).'">'.$slovo['slovo'].'</a>';
        if (!empty($slovo['vyslovnost'])) {
            echo ' <small>['.$slovo['vyslovnost'].']</small>';
        }
        echo ' <small>'.$druhy[$slovo['druh']].'</small>';
        echo '</dt>';
        echo '<dd>';

        echo markdown($slovo['vyznam']);

        if (!empty($plnaVerze)) { ?>
            <?php if (!empty($slovo['etymologie'])) {
  echo '<h5>Etymologie:</h5>';
  echo markdown($slovo['etymologie']);
} ?>

<?php if (!empty($slovo['priklady'])) {
    echo '<h5>Příklady použití slova:</h5>';
    echo markdown($slovo['priklady']);
} ?>

<?php if (!empty($slovo['mluvci'])) {
    echo '<h5>Slovo používá:</h5>';
    echo markdown($slovo['mluvci']);
} ?>

<?php if (!empty($slovo['zdroje'])) {
    echo '<h5>Další zdroje:</h5>';
    echo markdown($slovo['zdroje']);
} ?>
    <?php
        }

        echo '</dd>';
    }
    ?>
</dl>

<?php if (isset($tagy)) {
    $maxNum = reset($tagy);
    echo '<hr>';
    echo '<h3>Témata</h3>';
     foreach ($tagy as $tag=>$num) {
         $size = (($num / $maxNum) * 50) + 10;
         echo '<a href="/tagy/'.$tag.'" style="font-size: '.$size.'px;">#'.$tag.'</a> ';
    }
 } // tagy ?>


<?= render('_footer'); ?>
