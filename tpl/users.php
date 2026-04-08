<?=render('_header', ['title'=>'Obsluha']);?>

<h1>Obsluha</small></h1>

<?php if (count($users)) { ?>
    <table class="table">
        <tr><th>login</th><th>jméno</th><th></th></tr>
        <?php foreach ($users as $item) { ?>
            <tr>
                <td><code><?=$item['username']; ?></code>
                    <?php if ($item['is_superuser']) { ?> <span class="tag is-primary is-light">administrátor</span><?php } ?>
                    <?php if (!$item['is_active']) { ?> <span class="tag is-warning">neaktivní</span><?php } ?>
                </td>
                <td><?=$item['name']; ?><br><small><?=$item['note']; ?></small></td>
                <td><a href="/obsluha/upravit/<?=$item['id']; ?>/" class=""><span class="icon"><i class="fas fa-edit"></i></span></a> </td>
            </tr>
        <?php } ?>
    </table>
<?php } else {
    echo '<p>(nenalezeny žádné položky)</p><br>';
} ?>

<a href="/obsluha/pridat/" class="button is-primary">Přidat obsluhu</a>

<?=render('_footer');?>
