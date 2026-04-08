<?=render('_header', ['title'=>'Sklad']);?>

    <h1>Sklad</small></h1>

<?php if (count($items)) { ?>
    <table class="table">
        <tr><th>název</th><th>cena</th><th>pozn.</th><th>počet na skladě</th><th></th></tr>
        <?php foreach ($items as $item) { ?>
            <tr>
            <td><?=$item['name']; ?></td>
            <td><?=$item['price']; ?>,-</td>
            <td><?=$item['note']; ?></td>
            <td class="has-text-right">
            <?php
                $class = '';
                if ($item['amount']>0 && $item['amount']<5) $class = 'has-background-danger';
                if ($item['amount']>4 && $item['amount']<10) $class = 'has-background-warning';
            ?>
            <span class="<?=$class; ?>"><?=$item['amount']; ?></span>
            </td>
            <td><a href="/sklad/pocet/<?=$item['id']; ?>/" class=""><span class="icon"><i class="fas fa-edit"></i></span></a> </td>
            </tr>
        <?php } ?>
    </table>
<?php } else {
    echo '<p>(nenalezeny žádné položky)</p><br>';
} ?>

<?=render('_footer');?>