--TEST--
Serialize / Unserialize misbehaviour under OS with different bit numbers
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

if (PHP_INT_SIZE == 4) {
    $ser = 'cb41f2a05f20000000';
} else {
    $ser = 'cf000000012a05f200';
}

var_dump(unserialize(pack('H*', $ser)) == 5000000000);
?>
--EXPECT--
bool(true)
