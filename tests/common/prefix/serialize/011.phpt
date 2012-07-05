--TEST--
msgpacki_unserialize() crashes with invalid data
--SKIPIF--
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

var_dump(msgpacki_unserialize(pack('H*', 'cd03')));
var_dump(msgpacki_unserialize(pack('H*', '81c0a8737464436c61737320')));
var_dump(msgpacki_unserialize(pack('H*', '81c0da03e8737464436c617373')));
var_dump(msgpacki_unserialize(pack('H*', '8200a2313201')));
var_dump(msgpacki_unserialize(pack('H*', '8200a23132')));
var_dump(msgpacki_unserialize(pack('H*', '8200a2313201da03e8313233')));
var_dump(msgpacki_unserialize(pack('H*', 'da03e8313233')));
var_dump(msgpacki_unserialize(pack('H*', 'a0313233')));
?>
===DONE===
--EXPECTF--
Notice: MessagePack unserialize: Error at offset 0 of 2 bytes in %s011.php on line %d
bool(false)

Notice: MessagePack unserialize: Error at offset 11 of 12 bytes in %s011.php on line %d
bool(false)

Notice: MessagePack unserialize: Error at offset 1005 of 13 bytes in %s011.php on line %d
bool(false)

Notice: MessagePack unserialize: Error at offset 6 of 6 bytes in %s011.php on line %d
bool(false)

Notice: MessagePack unserialize: Error at offset 5 of 5 bytes in %s011.php on line %d
bool(false)

Notice: MessagePack unserialize: Error at offset 1009 of 12 bytes in %s011.php on line %d
bool(false)

Notice: MessagePack unserialize: Error at offset 1003 of 6 bytes in %s011.php on line %d
bool(false)

Notice: MessagePack unserialize: Error at offset 1 of 4 bytes in %s011.php on line %d
bool(false)
===DONE===
