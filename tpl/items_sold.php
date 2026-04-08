<?= render('_header', ['title'=>'Prodané položky']); ?>

<h1>Prodané položky</h1>

<table class="table">
<thead>
<tr><th>název</th><th>tento týden</th><th>minulý týden</th><th>tento měsíc</th><th>minulý měsíc</th></tr>
</thead>
<tbody>
<?php foreach ($items as $item) { $item_id =$item['id']; ?>
<tr><td><?=$item['name']; ?></td><td><?=$this_week[$item_id] ?? ''; ?></td><td><?=$last_week[$item_id] ?? ''; ?></td><td><?=$this_month[$item_id] ?? ''; ?></td><td><?=$last_month[$item_id] ?? ''; ?></td></tr>
<?php } ?>

<?php if (empty($items)) { ?>
<tr><td colspan="5"><em>žádné </em></td></tr>
<?php } ?>

</tbody>
</table>

<?= render('_footer'); ?>