<?php
// DEPENDENCIES
use severak\database\rows;
use severak\forms\form;

$dependencies['config'] = $config;
$singletons['pdo'] = function() {
    $config = di('config');
    return new PDO('sqlite:' . __DIR__ . '/' . $config['database']);
};
$singletons['rows'] = function(){
    return new severak\database\rows(di('pdo'));
};

$singletons['parsedown'] = function (){
  return new Parsedown();
};

function details($summary, $detail)
{
    if (!empty($detail)) {
        echo '<details>';
        echo '<summary>'.$summary.'</summary>';
        echo markdown($detail);
        echo '</details>';
    }
}

function markdown($text) {
    /** @var Parsedown $parserdown */
    $parserdown = di('parsedown');
    return  $parserdown->text($text);
}

$slovniDruhy = [
    1 => 'podstatné jméno',
    2 => 'přídavné jméno',
    3 => 'zájmeno',
    4 => 'číslovka',
    5 => 'sloveso',
    6 => 'příslovce',
    7 => 'předložka',
    8 => 'spojka',
    9 => 'částice',
    10 => 'citoslovce',
    11 => 'univerzální slovo',
    12 => 'zkratka',
    99 => 'ostatní'
];

// ROUTY

// HP & LOGIN
route('', '/', function (){
    /** @var severak\database\rows $rows */
    $rows = di('rows');
    $slova = $rows->more('slova', ['zobrazit'=>1], ['pismeno'=>'ASC', 'slovo'=>'ASC'], 999);

    $tagy = $rows->execute($rows->fragment('SELECT tag, COUNT(*) as cnt FROM slova_tagy GROUP BY tag ORDER BY cnt DESC'))->fetchAll(PDO::FETCH_KEY_PAIR);

    return render('slovnik', ['slova'=>$slova, 'tagy'=>$tagy]);
});

route('', '/tiskova-verze/', function (){
    /** @var severak\database\rows $rows */
    $rows = di('rows');
    $slova = $rows->more('slova', ['zobrazit'=>1], ['pismeno'=>'ASC', 'slovo'=>'ASC'], 999);

    return render('slovnik', ['slova'=>$slova, 'plnaVerze'=>true]);
});

route('', '/nova-slova/', function (){
    /** @var severak\database\rows $rows */
    $rows = di('rows');
    $slova = $rows->more('slova', ['zobrazit'=>1], ['aktualizovano'=>'DESC'], 10);

    return render('slovnik', ['slova'=>$slova, 'tema'=>'Nová nebo aktualizovaná slova']);
});

route('', '/tagy/{tag}', function ($req, $params){
    /** @var severak\database\rows $rows */
    $rows = di('rows');

    $slova = $rows
        ->with('slova_tagy', 'id', 'slovo_id', ['tag'=>urldecode($params['tag'])])
        ->more('slova', ['zobrazit'=>1], ['pismeno'=>'ASC', 'slovo'=>'ASC'], 999);

    return render('slovnik', ['slova'=>$slova, 'tema'=>urldecode($params['tag'])]);
});


route('GET', '/slovo/{slovo}', function ($req, $params) use ($slovniDruhy) {
    /** @var rows $rows */
    $rows = di('rows');

    $slovo = $rows->one('slova', ['slovo'=>urldecode($params['slovo'])]);
    if (!$slovo) {
        return notFound();
    }

    return render('slovo', ['slovo'=>$slovo, 'druhy'=>$slovniDruhy]);
});

route('GET', '/o-projektu/', function ($req, $params) use ($slovniDruhy) {
    /** @var rows $rows */
    $rows = di('rows');

    $slovo = $rows->one('slova', ['slovo'=>'_']);
    if (!$slovo) {
        return notFound();
    }

    return render('o-projektu', ['slovo'=>$slovo]);
});

// LOGIN ATD

route('', '/login/', function ($req){
    /** @var Psr\Http\Message\ServerRequestInterface $req */
    /** @var severak\database\rows $rows */
    $rows = di('rows');
    $form = new form(['method'=>'POST']);
    $form->field('username', ['required'=>true, 'label'=>'Jméno']);
    $form->field('password', ['type'=>'password', 'required'=>true, 'label'=>'Heslo']);
    $form->field('_login', ['type'=>'submit', 'label'=>'Přihlásit se']);

    if ($req->getMethod()=='POST') {
        $form->fill($req->getParsedBody());
        if ($form->validate()) {
            $uz = $rows->one('users', ['username'=>$form->values['username'], 'is_active'=>1]);
            if (!$uz) {
                $form->error('username', 'Uživatel nenalezen');
            } elseif (password_verify($form->values['password'], $uz['password'])) {
                unset($uz['password']);
                $_SESSION['user'] = $uz;
                return redirect('/');
            } else {
                $form->error('password', 'Špatné heslo.');
            }
        }
    }
    return render('form', ['form'=>$form]);
});

route('', '/logout/', function ($req){
    unset($_SESSION['user']);
    unset($_SESSION['flashes']);
    return redirect('/');
});

route('', '/zmena-hesla/', function ($req){
    if (!user()) return redirect('/login/');
    $user = user();
    /** @var Psr\Http\Message\ServerRequestInterface $req */
    /** @var severak\database\rows $rows */
    $rows = di('rows');

    $form = new form(['method'=>'post']);
    $form->field('password_current', ['required'=>true, 'type'=>'password', 'label'=>'Stávající heslo']);
    $form->field('password', ['required'=>true, 'type'=>'password', 'label'=>'Nové heslo']);
    $form->field('password_again', ['required'=>true, 'type'=>'password', 'label'=>'Nové heslo znovu']);
    $form->field('_sbt', ['label'=>'Změnit heslo', 'type'=>'submit']);

    $form->rule('password_again', function ($v, $o){
        return $v==$o['password'];
    }, 'Hesla se neshodují!');

    $uz = $rows->one('users', $user['id']);

    $form->rule('password_current', function ($v, $o) use ($uz) {
        return password_verify($v, $uz['password']);
    }, 'Špatné zadané současné heslo!');

    if ($req->getMethod()=='POST' && $form->fill($req->getParsedBody()) && $form->validate()) {
        $rows->update('users', [
            'password'=>password_hash($form->values['password'], PASSWORD_DEFAULT)
        ], [
            'id'=>$user['id']
        ]);
        flash('Heslo změněno.');
        return redirect('/');
    }

    return render('form', ['form'=>$form, 'title'=>'Změnit heslo']);
});

// SLOVA

route('GET', '/slova/', function (){
    if (!user()) return redirect('/login/');
    /** @var severak\database\rows $rows */
    $rows = di('rows');

    $page = $_GET['page'] ?? 1;
    $searchFor = $_GET['searchFor'] ?? null;
    $page = $_GET['page'] ?? 1;

    if ($searchFor) {
        $searchSql = '%' . $searchFor . '%';
        $slova = $rows->more('slova', $rows->fragment('slovo LIKE ? OR vyznam LIKE ? OR tagy LIKE ?', [$searchSql, $searchSql, $searchSql]));
        $pages = 1;
    } else {
        $slova = $rows->page('slova', [], ['slovo'=>'asc'], $page, 30);
        $pages = $rows->pages;
    }

    return render('slova', ['slova'=>$slova, 'page'=>$page, 'pages'=>$pages, 'searchFor'=>$searchFor]);
});

$slovoForm = new severak\forms\form(['method'=>'POST']);
$slovoForm->field('slovo', ['label'=>'Slovo', 'required'=>true]);
$slovoForm->field('druh', ['label'=>'Druh', 'required'=>true, 'type'=>'select', 'options'=>$slovniDruhy]);
$slovoForm->field('vyznam', ['label'=>'Význam', 'required'=>true, 'type'=>'textarea', 'rows'=>5]);
$slovoForm->field('vyslovnost', ['label'=>'Výslovnost', 'type'=>'text']);
$slovoForm->field('mluvci', ['label'=>'Kdo slovo používá?', 'type'=>'text']);
$slovoForm->field('etymologie', ['label'=>'Etymologie', 'type'=>'textarea', 'rows'=>3]);
$slovoForm->field('priklady', ['label'=>'Příklady', 'type'=>'textarea', 'rows'=>3]);
$slovoForm->field('zdroje', ['label'=>'Zdroje', 'type'=>'textarea', 'rows'=>3]);
$slovoForm->field('interni', ['label'=>'Interní poznámka', 'type'=>'textarea', 'rows'=>3]);
$slovoForm->field('tagy', ['label'=>'Tagy', 'type'=>'text']);

route('', '/slova/pridat/', function ($req) use ($slovoForm) {
    if (!user()) return redirect('/login/');
    $user = user();

    /** @var Psr\Http\Message\ServerRequestInterface $req */
    /** @var severak\database\rows $rows */
    $rows = di('rows');

    $form = $slovoForm;

    $form->field('_save', ['type'=>'submit', 'label'=>'Přidat']);

    if ($req->getMethod()=='POST' && $form->fill($req->getParsedBody()) && $form->validate()) {
        if ($form->isValid) {
            $rows->insert('slova', [
                'slovo' => $form->values['slovo'],
                'pismeno' => mb_substr($form->values['slovo'], 0, 1),
                'druh' => $form->values['druh'],
                'vyznam' => $form->values['vyznam'],
                'vyslovnost' => $form->values['vyslovnost'],
                'mluvci' => $form->values['mluvci'],
                'etymologie' => $form->values['etymologie'],
                'priklady' => $form->values['priklady'],
                'zdroje' => $form->values['zdroje'],
                'interni' => $form->values['interni'],
                'tagy' => $form->values['tagy'],
                'aktualizovano' => time()
            ]);

            flash('Slovo přidáno');
            return redirect('/slovo/'.urlencode($form->values['slovo']));
        }
    }

    return render('form', ['form'=>$form, 'title'=>'Přidat slovo']);
});

route('', '/slova/upravit/{id}/', function ($req, $params) use ($slovoForm) {
    if (!user()) return redirect('/login/');
    $user = user();

    /** @var Psr\Http\Message\ServerRequestInterface $req */
    /** @var severak\database\rows $rows */
    $rows = di('rows');

    $slovo = $rows->one('slova', (int) $params['id']);
    if (!$slovo) {
        return notFound();
    }

    $form = $slovoForm;
    $form->field('pismeno', ['label'=>'Zařadit pod písmeno']);
    $form->field('zobrazit', ['label'=>'Zobrazovat na webu?', 'type'=>'checkbox']);
    $form->field('_save', ['type'=>'submit', 'label'=>'Upravit']);

    $form->fill($slovo);

    if ($req->getMethod()=='POST' && $form->fill($req->getParsedBody()) && $form->validate()) {
        if ($form->isValid) {

            $rows->update('slova', [
                'slovo' => $form->values['slovo'],
                'druh' => $form->values['druh'],
                'vyznam' => $form->values['vyznam'],
                'vyslovnost' => $form->values['vyslovnost'],
                'mluvci' => $form->values['mluvci'],
                'etymologie' => $form->values['etymologie'],
                'priklady' => $form->values['priklady'],
                'zdroje' => $form->values['zdroje'],
                'interni' => $form->values['interni'],
                'tagy' => $form->values['tagy'],
                'pismeno' => $form->values['pismeno'],
                'zobrazit' => $form->values['zobrazit'],
                'aktualizovano' => time()
            ], (int) $params['id']);

            if (!empty($form->values['tagy'])) {
                $rows->delete('slova_tagy', ['slovo_id'=>$params['id']]);
                foreach (explode(' ', $form->values['tagy']) as $tag) {
                    $rows->insert('slova_tagy', ['slovo_id'=>$params['id'], 'tag'=>$tag]);
                }
            }

            flash('Slovo aktualizováno');
            return redirect('/slovo/'.urlencode($form->values['slovo']));
        }
    }

    return render('form', ['form'=>$form, 'title'=>'Přidat slovo']);
});


// OBSLUHA

route('GET', '/obsluha/', function ($req){
    if (!user()) return redirect('/login/');
    /** @var severak\database\rows $rows */
    $rows = di('rows');
    $items = $rows->page('users', [], ['is_active'=>'desc', 'name'=>'asc']);

    return render('users', ['users'=>$items]);
});

route('', '/obsluha/pridat/', function ($req){
    if (!user()) return redirect('/login/');
    $user = user();
    if (!$user['is_superuser']) {
        flash('Obsluhu může přidávat jen admin.', 'warning');
        return redirect('/');
    }

    /** @var Psr\Http\Message\ServerRequestInterface $req */
    /** @var severak\database\rows $rows */
    $rows = di('rows');

    $form = new form(['method'=>'post']);
    $form->field('username', ['label'=>'Uživatelské jméno']);
    $form->field('password', ['required'=>true, 'type'=>'password', 'label'=>'Heslo']);
    $form->field('password_again', ['required'=>true, 'type'=>'password', 'label'=>'Heslo znovu']);
    $form->field('name', ['required'=>true, 'type'=>'text', 'label'=>'Jméno']);
    $form->field('card_id', ['type'=>'number', 'label'=>'Číslo členské karty', 'id'=>'qrcode']);
    $form->field('_sbt', ['label'=>'Přidat', 'type'=>'submit']);

    $form->rule('password_again', function ($v, $o){
        return $v==$o['password'];
    }, 'Hesla se neshodují!');

    if ($req->getMethod()=='POST' && $form->fill($req->getParsedBody()) && $form->validate()) {
        $duplicateUser = $rows->one('users', ['username'=>$form->values['username'] ]);
        if ($duplicateUser) {
            $form->error('username', 'Uživatel tohoto jména již v systému je.');
        }

        $memberId = null;
        if ($form->values['card_id']) {
            $card = $rows->one('cards', ['id'=>$form->values['card_id'], 'is_active'=>1]);
            $memberId = $card['member_id'];
        }

        if ($form->isValid) {
            $rows->insert('users', [
                'username' => $form->values['username'],
                'name' => $form->values['name'],
                'password' => password_hash($form->values['password'], PASSWORD_DEFAULT),
                'member_id'=> $memberId
            ]);

            flash('Uživatel přidán.', 'success');
            return redirect('/obsluha/');
        }
    }

    return render('form', ['form'=>$form, 'title'=>'Přidat obsluhu']);
});

route('', '/obsluha/upravit/{id}/', function ($req, $params){
    if (!user()) return redirect('/login/');
    $user = user();

    if (!$user['is_superuser']) {
        flash('Obsluhu může upravovat jen admin.', 'warning');
        return redirect('/');
    }

    $id = $params['id'];

    /** @var Psr\Http\Message\ServerRequestInterface $req */
    /** @var severak\database\rows $rows */
    $rows = di('rows');

    $form = new form(['method'=>'post']);
    $form->field('username', ['label'=>'Uživatelské jméno']);
    $form->field('password', ['type'=>'password', 'label'=>'Heslo']);
    $form->field('password_again', ['type'=>'password', 'label'=>'Heslo znovu']);
    $form->field('name', ['required'=>true, 'type'=>'text', 'label'=>'Jméno']);
    $form->field('card_id', ['type'=>'number', 'label'=>'Číslo členské karty', 'id'=>'qrcode']);
    $form->field('is_active', ['type'=>'checkbox', 'label'=>'Aktivní?']);
    $form->field('is_superuser', ['type'=>'checkbox', 'label'=>'Je admin?']);
    $form->field('note', ['type'=>'textarea', 'label'=>'Poznámka']);
    $form->field('_sbt', ['label'=>'Uložit', 'type'=>'submit']);

    $form->rule('password_again', function ($v, $o){
        return $v==$o['password'];
    }, 'Hesla se neshodují!');

    if ($req->getMethod()=='POST' && $form->fill($req->getParsedBody())) {
        $form->validate();

        $duplicateUser = $rows->one('users', ['username'=>$form->values['username'] ]);
        if ($duplicateUser && $duplicateUser['id']!=$id) {
            $form->error('username', 'Uživatel tohoto jména již v systému je.');
        }

        if ($form->values['password'] && $form->values['password']!=$form->values['password_again']) {
            $form->error('password', 'Hesla se musí shodovat!');
        }

        if ($form->isValid) {
            $update = $form->values; // TODO tohle je prasárna
            unset($update['id'], $update['password'], $update['password_again'], $update['card_id'], $update['_sbt']);
            if ($form->values['password'] && $form->values['password']!=$form->values['password_again']) {
                $update['password'] = password_hash($form->values['password'], PASSWORD_DEFAULT);
            }

            if ($form->values['card_id']) {
                $card = $rows->one('cards', ['id'=>$form->values['card_id'], 'is_active'=>1]);
                $update['member_id'] = $card['member_id'];
            }

            $rows->update('users', $update, $id);

            flash('Uživatel upraven.', 'success');
            return redirect('/obsluha/');
        }

    } else {
        $editedUser = $rows->one('users', $id);

        unset($editedUser['password']);

        if ($editedUser['member_id']) {
            $card = $rows->one('cards', ['member_id'=>$editedUser['member_id'], 'is_active'=>1]);
            if ($card) {
                $editedUser['card_id'] = $card['id'];
            }
        }

        $form->fill($editedUser);
    }

    return render('form', ['form'=>$form, 'title'=>'Upravit obsluhu']);
});
