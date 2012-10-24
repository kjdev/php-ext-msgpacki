--TEST--
Test serialize() & unserialize() functions: error conditions - wrong number of args.
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo "*** Testing serialize()/unserialize() : error conditions ***\n";

// Zero arguments
var_dump( serialize() );
var_dump( unserialize() );

//Test serialize with one more than the expected number of arguments
var_dump( serialize(1,2) );
var_dump( unserialize(1,$status,2) );

echo "Done";
?>
--EXPECTF--
*** Testing serialize()/unserialize() : error conditions ***

Warning: MessagePacki\serialize() expects exactly 1 parameter, 0 given in %s on line 11
bool(false)

Warning: MessagePacki\unserialize() expects at least 1 parameter, 0 given in %s on line 12
bool(false)

Warning: MessagePacki\serialize() expects exactly 1 parameter, 2 given in %s on line 15
bool(false)

Warning: MessagePacki\unserialize() expects at most 2 parameters, 3 given in %s on line 16
bool(false)
Done
