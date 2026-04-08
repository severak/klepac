<?=render('_header', ['title'=>'Členové']);?>

<form method="get">
    <div class="field has-addons">
        <div class="control is-expanded">
            <input name="searchFor" class="input" type="search" placeholder="část jména, emailové adresy nebo telefonu" value="<?=$searchFor; ?>">
        </div>
        <div class="control">
            <input type="submit" class="button is-info" value="Najít">
        </div>
    </div>
</form>
<br>

<h1>Členové</small></h1>

<?php if (count($members)) { ?>



    <table class="table">
        <tr><th>jméno</th><th>pozn.</th><th></th><th></th></tr>
        <?php foreach ($members as $member) { ?>
            <tr>
                <td><?=$member['name']; ?><?php if (!$member['is_active']) { ?> <span class="tag is-warning">neaktivní</span><?php } ?></td>
                <td><?=$member['note']; ?></td>
                <td><a href="/clenove/detail/<?=$member['id']; ?>/" class=""><span class="icon"><i class="fas fa-search"></i></span></a> </td>
                <td><a href="/clenove/upravit/<?=$member['id']; ?>/" class=""><span class="icon"><i class="fas fa-edit"></i></span></a> </td>
            </tr>
        <?php } ?>
    </table>

    <?=render('_pagination', ['page'=>$page, 'pages'=>$pages]); ?>
<?php } else {
    echo '<p>(nenalezeny žádné položky)</p><br>';
} ?>

<a href="/clenove/pridat/" class="button is-primary">Přidat člena</a>

<br><br>
<form method="post">
    <div class="field has-addons">
        <div class="control is-expanded">
            <input name="qrcode" class="input" type="search" placeholder="QR kód kartičky" id="qrcode">
        </div>
        <div class="control">
            <input type="submit" class="button is-info" value="QR!">
        </div>
    </div>
</form>

<?=render('_footer');?>
