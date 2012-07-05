--TEST--
(un)serializing __PHP_Incomplete_Class instance
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$d = msgpacki_serialize(new __PHP_Incomplete_Class);
$o = msgpacki_unserialize($d);
var_dump($o);

$o->test = "a";
var_dump($o->test);
var_dump($o->test2);

echo "Done\n";
?>
--EXPECTF--
object(__PHP_Incomplete_Class)#%d (0) {
}

Notice: main(): The script tried to execute a method or access a property of an incomplete object. Please ensure that the class definition "unknown" of the object you are trying to operate on was loaded _before_ unserialize() gets called or provide a __autoload() function to load the class definition  in %s on line %d

Notice: main(): The script tried to execute a method or access a property of an incomplete object. Please ensure that the class definition "unknown" of the object you are trying to operate on was loaded _before_ unserialize() gets called or provide a __autoload() function to load the class definition  in %s on line %d
NULL

Notice: main(): The script tried to execute a method or access a property of an incomplete object. Please ensure that the class definition "unknown" of the object you are trying to operate on was loaded _before_ unserialize() gets called or provide a __autoload() function to load the class definition  in %s on line %d
NULL
Done
