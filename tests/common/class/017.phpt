--TEST--
MessagePacki: resources
--FILE--
<?php

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$m = new MessagePacki();

echo "\n--- Testing Resource ---\n";
$file_handle = fopen( __FILE__, "r" );
$serialized_data = $m->pack( $file_handle );
fclose($file_handle);
var_dump(bin2hex($serialized_data));
var_dump($m->unpack($serialized_data));

echo "\nDone";
?>
--EXPECTF--
--- Testing Resource ---
string(2) "00"
int(%d)

Done