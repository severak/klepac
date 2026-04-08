<?=render('_header', ['title'=>'Slova']);?>

<form method="get">
    <div class="field has-addons">
        <div class="control is-expanded">
            <input name="searchFor" class="input" type="search" placeholder="slovo" value="<?=$searchFor; ?>">
        </div>
        <div class="control">
            <input type="submit" class="button is-info" value="Najít">
        </div>
    </div>
</form>
<br>

<h1>Slova</h1>

<?php if (count($slova)) { ?>
    <table class="table">
        <tr><th>jméno</th><th></th><th></th></tr>
        <?php foreach ($slova as $slovo) { ?>
            <tr>

                <td><?=$slovo['slovo']; ?></td>
                <td><a href="/slovo/<?=$slovo['slovo']; ?>" class=""><span class="icon"><i class="fas fa-search"></i></span></a> </td>
                <td><a href="/slova/upravit/<?=$slovo['id']; ?>/" class=""><span class="icon"><i class="fas fa-edit"></i></span></a> </td>
            </tr>
        <?php } ?>
    </table>

    <?=render('_pagination', ['page'=>$page, 'pages'=>$pages]); ?>
<?php } else {
    echo '<p>(nenalezeny žádné položky)</p><br>';
} ?>

<a href="/slova/pridat/" class="button is-primary">Přidat slovo</a>

<?=render('_footer');?>
