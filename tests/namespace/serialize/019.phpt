--TEST--
zend_ptr_stack reallocation problem
--INI--
error_reporting=0
--FILE--
<?php

namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class test {
    function extend_zend_ptr_stack($count,$a,$b,$c,$d,$e) {
        if ($count>0) $this->extend_zend_ptr_stack($count - 1,$a,$b,$c,$d,$e);
    }

    function __wakeup() {
        $this->extend_zend_ptr_stack(10,'a','b','c','d','e');
    }
}

$str=pack('H*', '820081c0b04d6573736167655061636b5c74657374');
var_dump(unserialize($str));
--EXPECT--
bool(false)
