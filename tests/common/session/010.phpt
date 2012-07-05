--TEST--
$session_array = explode(";", session_encode()); should not segfault
--INI--
session.use_cookies=0
session.cache_limiter=
session.serialize_handler=msgpacki
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

error_reporting(E_ALL);

$session_array = explode(";", @session_encode());
print "I live\n";
?>
--EXPECT--
I live
