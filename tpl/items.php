<?=render('_header', ['title'=>'Sklad / stálé položky']);?>

<h1>Sklad <small>/ stálé položky</small></h1>

<?php if (count($items)) { ?>
<table class="table">
    <tr><th>název</th><th>cena</th><th>počet</th><th></th><th></th></tr>
    <?php foreach ($items as $item) { ?>
        <tr>
            <td><?=$item['name']; ?><br><small><?=$item['note']; ?></small></td>
            <td><?=$item['price']; ?>,-</td>
            <td class="has-text-right">
                <?php
                $class = 'is-success is-light';
                if ($item['amount']<5) $class = 'is-danger';
                if ($item['amount']>4 && $item['amount']<10) $class = 'is-warning';
                if ($item['is_amount_tracked']) {
                ?>
                <span class="tag <?=$class; ?>"><?=$item['amount']; ?></span>
                <?php } else {  ?>
                    ---
                <?php } ?>
            </td>
            <td><a href="/sklad/upravit/<?=$item['id']; ?>/" class=""><span class="icon"><i class="fas fa-edit"></i></span></a> </td>
            <td><a href="/sklad/smazat/<?=$item['id']; ?>/" class="delete" onclick="return confirm('Opravdu chcete položku smazat?')">smazat</a> </td>
        </tr>
    <?php } ?>
</table>
<?php } else {
    echo '<p>(nenalezeny žádné položky)</p><br>';
} ?>

<a href="/sklad/pridat/" class="button is-primary">Přidat položku</a>

<br><br>
<a href="/sklad/prodano/" class="button">Prodané položky</a>

<?=render('_footer');?>