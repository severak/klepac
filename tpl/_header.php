<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php if (!empty($title)) echo $title  . ' - ' ; ?>Slovník méně častých slov</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
</head>
<body>
<nav class="navbar has-background-grey-lighter" role="navigation" aria-label="main navigation">
    <div class="container" style="max-width: 800px">
    <div class="navbar-brand">
        <a class="navbar-item slovnik-nadpis" href="/">
            <img src="/favicon-32x32.png"/>&nbsp;Slovník méně častých slov
        </a>

        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample" id="navbar-burger">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbar-menu" class="navbar-menu">
        <div class="navbar-start">
            <?php if (user()) { ?>
            <a class="navbar-item" href="/slova/">
                slova
            </a>

            <a class="navbar-item" href="/slova/pridat/">
                přidat slovo
            </a>

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    ostatní
                </a>

                <div class="navbar-dropdown">
                    <?php if ((user())['is_superuser']) { ?>
                    <a class="navbar-item" href="/obsluha/">
                        obsluha
                    </a>

                    <a class="navbar-item" href="/o-projektu/">
                        o projektu
                    </a>

                    <a class="navbar-item" href="/tiskova-verze/">
                        k vytisknutí
                    </a>

                    <a class="navbar-item" href="/nova-slova/">
                        nová slova
                    </a>

                    <hr class="navbar-divider">
                    <?php } ?>
                    <a class="navbar-item" href="/zmena-hesla/">
                        změnit heslo
                    </a>
                </div>
            </div>
            <?php } else { ?>
                <a class="navbar-item" href="/o-projektu/">
                    o projektu
                </a>

                <a class="navbar-item" href="/tiskova-verze/">
                    k vytisknutí
                </a>

                <a class="navbar-item" href="/nova-slova/">
                    nová slova
                </a>
            <?php } ?>
        </div>

        <div class="navbar-end">
            <?php if (user()) { ?>
            <div class="navbar-item">
                <div class="buttons">
                    <a class="button is-light" href="/logout/">
                        odhlásit
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    </div>
</nav>
<section class="section" id="app">
    <div class="container" style="max-width: 800px">
        <?php if (isset($_SESSION['flashes'])) {
            foreach ($_SESSION['flashes'] as $flashtype=>$messages) {
                foreach ($messages as $message) {
                    echo '<div class="message is-'.$flashtype.'"><div class="message-body">'.$message.'</div></div>';
                }
                unset($_SESSION['flashes'][$flashtype]);
            }
        }
        ?>
        <main>
