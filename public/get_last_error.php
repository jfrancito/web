<?php
$log = file(__DIR__ . '/../storage/logs/laravel.log');
$errors = preg_grep('/local\.ERROR:/', $log);
echo array_pop($errors);
