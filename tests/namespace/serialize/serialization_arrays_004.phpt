--TEST--
serialization: arrays with references to the containing array
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

function check(&$a) {
    var_dump($a);
    $ser = serialize($a);
    var_dump(bin2hex($ser));

    $b = unserialize($ser);
    var_dump($b);
    $b[0] = "b0.changed";
    var_dump($b);
    $b[1] = "b1.changed";
    var_dump($b);
    $b[2] = "b2.changed";
    var_dump($b);
}

echo "\n\n--- 1 refs container:\n";
$a = array();
$a[0] = &$a;
$a[1] = 1;
$a[2] = 1;
check($a);

echo "\n\n--- 1,2 ref container:\n";
$a = array();
$a[0] = &$a;
$a[1] = &$a;
$a[2] = 1;
check($a);

echo "\n\n--- 1,2,3 ref container:\n";
$a = array();
$a[0] = &$a;
$a[1] = &$a;
$a[2] = &$a;
check($a);

echo "Done";
?>
--EXPECTF--
--- 1 refs container:
array(3) {
  [0]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    int(1)
    [2]=>
    int(1)
  }
  [1]=>
  int(1)
  [2]=>
  int(1)
}
string(34) "8300830082c00100020101020101010201"
array(3) {
  [0]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    int(1)
    [2]=>
    int(1)
  }
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  string(10) "b2.changed"
}


--- 1,2 ref container:
array(3) {
  [0]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    *RECURSION*
    [2]=>
    int(1)
  }
  [1]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    *RECURSION*
    [2]=>
    int(1)
  }
  [2]=>
  int(1)
}
string(50) "8300830082c00100020182c001000202010182c00100020201"
array(3) {
  [0]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    *RECURSION*
    [2]=>
    int(1)
  }
  [1]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    *RECURSION*
    [2]=>
    int(1)
  }
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  &string(10) "b0.changed"
  [1]=>
  &string(10) "b0.changed"
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  &string(10) "b1.changed"
  [1]=>
  &string(10) "b1.changed"
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  &string(10) "b1.changed"
  [1]=>
  &string(10) "b1.changed"
  [2]=>
  string(10) "b2.changed"
}


--- 1,2,3 ref container:
array(3) {
  [0]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    *RECURSION*
    [2]=>
    *RECURSION*
  }
  [1]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    *RECURSION*
    [2]=>
    *RECURSION*
  }
  [2]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    *RECURSION*
    [2]=>
    *RECURSION*
  }
}
string(66) "8300830082c00100020182c00100020282c00100020182c00100020282c0010002"
array(3) {
  [0]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    *RECURSION*
    [2]=>
    *RECURSION*
  }
  [1]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    *RECURSION*
    [2]=>
    *RECURSION*
  }
  [2]=>
  &array(3) {
    [0]=>
    *RECURSION*
    [1]=>
    *RECURSION*
    [2]=>
    *RECURSION*
  }
}
array(3) {
  [0]=>
  &string(10) "b0.changed"
  [1]=>
  &string(10) "b0.changed"
  [2]=>
  &string(10) "b0.changed"
}
array(3) {
  [0]=>
  &string(10) "b1.changed"
  [1]=>
  &string(10) "b1.changed"
  [2]=>
  &string(10) "b1.changed"
}
array(3) {
  [0]=>
  &string(10) "b2.changed"
  [1]=>
  &string(10) "b2.changed"
  [2]=>
  &string(10) "b2.changed"
}
Done
