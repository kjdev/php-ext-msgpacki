--TEST--
serialization: arrays with empty
--FILE--
<?php

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$items = array();
foreach (range(0, 1024) as $r) {
    $items[] = array('foo' => array());
}
var_dump(count(msgpacki_unserialize(msgpacki_serialize($items))));

?>
--EXPECTF--
int(1025)
