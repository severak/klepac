<?php
// path to database
$config['database'] = 'db/stela.sqlite';
// MD5 of adminer password
$config['adminer_password'] = md5('heslo');
// secret key (throw something from random.org here)
$config['secret'] = '123456789';
return $config;
