<?php
// universal bulma CSS styled form
// arguments:
// - title
// - form

echo render('_header', ['title'=>$title ?? null]);

if (!empty($title)) {
    echo '<h1>'.$title.'</h1>';
}

$F = new severak\forms\html($form);
echo $F->open();

foreach ($F->fields as $fieldName) {
	
	echo '<div class="field">';
	
	echo $F->label($fieldName, ['class'=>'label']);

	echo '<div class="control">';
	$attr = ['class'=>'input'];
	if ($form->fields[$fieldName]['type']=='submit') $attr['class'] = 'button is-primary';
	if ($form->fields[$fieldName]['type']=='textarea') $attr['class'] = 'textarea';
	if ($form->fields[$fieldName]['type']=='checkbox') $attr['class'] = 'checkbox';
	
	echo $F->field($fieldName, $attr);
	if (!empty($form->errors[$fieldName])) {
		echo ' <p class="help is-danger">' . htmlspecialchars($form->errors[$fieldName]) . '</p>';
	}
	echo '</div></div>';
}

echo $F->close();

echo render('_footer');