--TEST--
msgpacki_unserialize broken on 64-bit systems
--FILE--
<?php

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo msgpacki_unserialize(msgpacki_serialize(2147483648));
?>
--EXPECT--
2147483648
