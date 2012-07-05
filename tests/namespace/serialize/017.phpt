--TEST--
unserialize broken on 64-bit systems
--FILE--
<?php

namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo unserialize(serialize(2147483648));
?>
--EXPECT--
2147483648
