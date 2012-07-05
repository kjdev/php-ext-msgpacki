--TEST--
unserialize() floats with E notation
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

foreach(array(1e2, 5.2e25, 85.29e-23, 9e-9) AS $value) {
    $ser = serialize($value);
    echo bin2hex($ser)."\n";
    var_dump(unserialize($ser));
    echo "\n";
}
?>
===DONE===
--EXPECTREGEX--
cb4059000000000000
float\(100\)

cb454581b6d300d022
float\(5\.2E\+25\)

cb3b901c5f0f3bd16b
float\(8\.529E-22\)

cb3e4353cd652bb167
float\(9\.0E-9\)

===DONE===
