--TEST--
Object serialization / unserialization: real references and COW references
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

echo "\n\nArray containing same object twice:\n";
$obj = new stdclass;
$a[0] = $obj;
$a[1] = $a[0];
var_dump($a);

$ser = msgpacki_encode($a);
var_dump(bin2hex($ser));

$ua = msgpacki_decode($ser);
var_dump($ua);
$ua[0]->a = "newProp";
var_dump($ua);
$ua[0] = "a0.changed";
var_dump($ua);


echo "\n\nArray containing object and reference to that object:\n";
$obj = new stdclass;
$a[0] = $obj;
$a[1] = &$a[0];
var_dump($a);

$ser = msgpacki_encode($a);
var_dump(bin2hex($ser));

$ua = msgpacki_decode($ser);
var_dump($ua);
$ua[0]->a = "newProp";
var_dump($ua);
$ua[0] = "a0.changed";
var_dump($ua);

echo "\n\nObject containing same object twice:";
$obj = new stdclass;
$contaner = new stdclass;
$contaner->a = $obj;
$contaner->b = $contaner->a;
var_dump($contaner);

$ser = msgpacki_encode($contaner);
var_dump(bin2hex($ser));

$ucontainer = msgpacki_decode($ser);
var_dump($ucontainer);
$ucontainer->a->a = "newProp";
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    echo "\nWarning: Attempt to modify property of non-object in ", __FILE__, " on line ",  __LINE__, "\n";
}
var_dump($ucontainer);
$ucontainer->a = "container->a.changed";
var_dump($ucontainer);


echo "\n\nObject containing object and reference to that object:\n";
$obj = new stdclass;
$contaner = new stdclass;
$contaner->a = $obj;
$contaner->b = &$contaner->a;
var_dump($contaner);

$ser = msgpacki_encode($contaner);
var_dump(bin2hex($ser));

$ucontainer = msgpacki_decode($ser);
var_dump($ucontainer);
$ucontainer->a->a = "newProp";
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    echo "\nWarning: Attempt to modify property of non-object in ", __FILE__, " on line ",  __LINE__, "\n";
}
var_dump($ucontainer);
$ucontainer->b = "container->a.changed";
var_dump($ucontainer);

echo "Done";
?>
--EXPECTF--


Array containing same object twice:
array(2) {
  [0]=>
  object(stdClass)#%d (0) {
  }
  [1]=>
  object(stdClass)#%d (0) {
  }
}
string(6) "928080"
array(2) {
  [0]=>
  array(0) {
  }
  [1]=>
  array(0) {
  }
}

Warning: Attempt to assign property of non-object in %s on line %d
array(2) {
  [0]=>
  array(0) {
  }
  [1]=>
  array(0) {
  }
}
array(2) {
  [0]=>
  string(10) "a0.changed"
  [1]=>
  array(0) {
  }
}


Array containing object and reference to that object:
array(2) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  &object(stdClass)#%d (0) {
  }
}
string(6) "928080"
array(2) {
  [0]=>
  array(0) {
  }
  [1]=>
  array(0) {
  }
}

Warning: Attempt to assign property of non-object in %s on line %d
array(2) {
  [0]=>
  array(0) {
  }
  [1]=>
  array(0) {
  }
}
array(2) {
  [0]=>
  string(10) "a0.changed"
  [1]=>
  array(0) {
  }
}


Object containing same object twice:object(stdClass)#%d (2) {
  ["a"]=>
  object(stdClass)#%d (0) {
  }
  ["b"]=>
  object(stdClass)#%d (0) {
  }
}
string(14) "82a16180a16280"
array(2) {
  ["a"]=>
  array(0) {
  }
  ["b"]=>
  array(0) {
  }
}

Warning: Attempt to modify property of non-object in %s on line %d
array(2) {
  ["a"]=>
  array(0) {
  }
  ["b"]=>
  array(0) {
  }
}

Warning: Attempt to assign property of non-object in %s on line %d
array(2) {
  ["a"]=>
  array(0) {
  }
  ["b"]=>
  array(0) {
  }
}


Object containing object and reference to that object:
object(stdClass)#%d (2) {
  ["a"]=>
  &object(stdClass)#%d (0) {
  }
  ["b"]=>
  &object(stdClass)#%d (0) {
  }
}
string(14) "82a16180a16280"
array(2) {
  ["a"]=>
  array(0) {
  }
  ["b"]=>
  array(0) {
  }
}

Warning: Attempt to modify property of non-object in %s on line %d
array(2) {
  ["a"]=>
  array(0) {
  }
  ["b"]=>
  array(0) {
  }
}

Warning: Attempt to assign property of non-object in %s on line %d
array(2) {
  ["a"]=>
  array(0) {
  }
  ["b"]=>
  array(0) {
  }
}
Done
