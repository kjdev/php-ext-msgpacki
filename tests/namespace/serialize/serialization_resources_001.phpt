--TEST--
Test serialize() & unserialize() functions: resources
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo "\n--- Testing Resource ---\n";
$file_handle = fopen( __FILE__, "r" );
$serialized_data = serialize( $file_handle );
fclose($file_handle);
var_dump(bin2hex($serialized_data));
var_dump(unserialize($serialized_data));

echo "\nDone";
?>
--EXPECTF--
--- Testing Resource ---
string(2) "00"
int(%d)

Done