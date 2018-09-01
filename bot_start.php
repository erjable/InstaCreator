<?php
require('class.php');
@set_time_limit(0);
@clearstatcache();
@ini_set('max_execution_time',0);
@ini_set('output_buffering',0);
echo "[?] Kac adet hesap olusturulsun:";
$count = trim(fgets(STDIN, 1024));
echo "[?] Kac saniyede bir olusturulsun:";
$sleep = trim(fgets(STDIN, 1024));
echo "[!] Lutfen bekleyiniz. . .\n\n";
$i = new instaCreator();
$i->userCreate($count,$sleep);