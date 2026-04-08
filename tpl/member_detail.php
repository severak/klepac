<?=render('_header', ['title'=>'detail člena']);?>

<div class="message" >
    <div class="message-header">Detail člena</div>
    <div class="message-body">
        <div class="columns is-mobile">
            <div class="column">
                <?php if (!$member['is_active']) { ?><span class="tag is-warning">neaktivní</span> <?php } ?>
                <span class="icon"><span class="icon fas fa-user"></span></span><?=$member['name']; ?></div>
            <div class="column has-text-right"><?=$member['balance']; ?>,-</div>
        </div>
        <div class="columns">
            <div class="column">
                <?php if ($member['phone']) { ?>
                <span class="icon"><span class="icon fas fa-phone"></span></span><?=$member['phone']; ?>
                <?php } ?>
            </div>
            <div class="column has-text-right">
                <?php if ($member['email']) { ?>
                <span class="icon"><span class="fas fa-envelope"></span></span><?=$member['email']; ?>
                <?php } ?>
            </div>
        </div>
        <div class="content">
        <u>karty:</u>
        <ul><?php
            foreach ($cards as $card) {
                echo '<li>';
                if ($card['is_active']) echo '<strong>';

                echo $card['id']. ' <em>' . $card['note'] . '</em>';

                if ($card['is_active']) echo '</strong>';
                echo '</li>';
            } ?>
        </ul>

        <p><?=$member['note']; ?></p>
        </div>
        <a href="/clenove/upravit/<?=$member['id']; ?>/" class="button">upravit</a>
        <a href="/clenove/nova_karta/<?=$member['id']; ?>/" class="button">vystavit novou kartu</a>
    </div>
</div>

<?php if (count($transactions)) {  ?>

    <h2 class="subtitle">poslední provedené transakce</h2>

    <table class="table">
        <tr><th>čas</th><th>částka</th><th></th><th></th></tr>
        <?php foreach ($transactions as $tsx) {
        $datum = date_create('@'.$tsx['issued_at']);
        ?>
            <tr>
                <td><?=$datum ? $datum->format('j.n.Y H:i') : ''; ?></td>
                <td><?=$tsx['amount']; ?>,-</td>
                <td>


                <?php if ($tsx['is_cash']) {
                    echo 'dobytí';
                } else if (json_decode($tsx['items'])) {
                    echo '<details><summary>nákup</summary><ul>';
                    $items = json_decode($tsx['items'], true);
                    foreach ($items as $item) {
                        echo '<li>'.$item['name'] . '';
                        if ($item['amount']>1) echo '(' . $item['amount'] . '*)';
                        echo ' ' . $item['total'] . ',-</li>';
                    }

                    echo '</ul></details>';
                } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?=render('_pagination', ['page'=>$page, 'pages'=>$pages]); ?>
<?php  } else {
    echo '<p>(nenalezeny žádné položky)</p><br>';
} ?>

<?=render('_footer');?>
