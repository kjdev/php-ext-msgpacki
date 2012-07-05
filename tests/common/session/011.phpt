--TEST--
session_decode(); should not segfault
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

@session_decode("garbage data and no session started");
@session_decode(pack('H*', "82a6757365726964a56d617a656ea863686174526f6f6d01"));
print "I live\n";
?>
--EXPECT--
I live
