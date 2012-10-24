--TEST--
Test msgpacki_serialize() & msgpacki_unserialize() functions: error conditions - wrong number of args.
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo "*** Testing msgpacki_serialize()/msgpacki_unserialize() : error conditions ***\n";

// Zero arguments
var_dump( msgpacki_serialize() );
var_dump( msgpacki_unserialize() );

//Test serialize with one more than the expected number of arguments
var_dump( msgpacki_serialize(1,2) );
var_dump( msgpacki_unserialize(1,$status,2) );

echo "Done";
?>
--EXPECTF--
*** Testing msgpacki_serialize()/msgpacki_unserialize() : error conditions ***

Warning: msgpacki_serialize() expects exactly 1 parameter, 0 given in %s on line 11
bool(false)

Warning: msgpacki_unserialize() expects at least 1 parameter, 0 given in %s on line 12
bool(false)

Warning: msgpacki_serialize() expects exactly 1 parameter, 2 given in %s on line 15
bool(false)

Warning: msgpacki_unserialize() expects at most 2 parameters, 3 given in %s on line 16
bool(false)
Done
