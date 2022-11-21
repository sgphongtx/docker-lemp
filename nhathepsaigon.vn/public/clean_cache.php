<?php
opcache_reset();
apc_clear_cache();
apc_clear_cache('user');
echo $_SERVER['SERVER_ADDR'] . "\n";
?>