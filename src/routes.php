<?php

require_once __DIR__ . '/router.php';
get('/', fn () => redirect('/index.html'));
get('/404', 'views/404.php');
get('/index.html', 'views/index.php');
get('/admin/index.html', 'views/admin/index.php');
post('/api/import.php', 'api/import.php');
