--TEST--
serialization: arrays with references, nested
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

function check(&$a) {
    var_dump($a);
    $ser = encode($a);
    var_dump(bin2hex($ser));

    $b = decode($ser);

    // Change each element and dump result.
    foreach($b as $k=>$v) {
        if (is_array($v)){
            foreach($b[$k] as $sk=>$sv) {
                $b[$k][$sk] = "b$k.$sk.changed";
                var_dump($b);
            }
        } else {
            $b[$k] = "b$k.changed";
            var_dump($b);
        }
    }
}

echo "\n\n--- Nested array references 1 element in containing array:\n";
$a = array();
$c = array(1,1,&$a);
$a[0] = &$c[0];
$a[1] = 1;
check($c);

echo "\n\n--- Nested array references 1 element in containing array (slightly different):\n";
$a = array();
$c = array(1,&$a,1);
$a[0] = 1;
$a[1] = &$c[0];
check($c);

echo "\n\n--- Nested array references 2 elements in containing array:\n";
$a = array();
$c = array(1,1,&$a);
$a[0] = &$c[0];
$a[1] = &$c[1];
check($c);


echo "\n\n--- Containing array references 1 element in nested array:\n";
$a = array();
$a[0] = 1;
$a[1] = 1;
$c = array(1,&$a[0],&$a);
check($c);

echo "\n\n--- Containing array references 2 elements in nested array:\n";
$a = array();
$a[0] = 1;
$a[1] = 1;
$c = array(&$a[0],&$a[1],&$a);
check($c);

echo "\n\n--- Nested array references container:\n";
$a = array();
$c = array(1,1,&$a);
$a[0] = 1;
$a[1] = &$c;
check($c);

?>
--EXPECTF--
--- Nested array references 1 element in containing array:
array(3) {
  [0]=>
  &int(1)
  [1]=>
  int(1)
  [2]=>
  &array(2) {
    [0]=>
    &int(1)
    [1]=>
    int(1)
  }
}
string(12) "930101920101"
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    string(12) "b2.0.changed"
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    string(12) "b2.0.changed"
    [1]=>
    string(12) "b2.1.changed"
  }
}


--- Nested array references 1 element in containing array (slightly different):
array(3) {
  [0]=>
  &int(1)
  [1]=>
  &array(2) {
    [0]=>
    int(1)
    [1]=>
    &int(1)
  }
  [2]=>
  int(1)
}
string(12) "930192010101"
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  array(2) {
    [0]=>
    string(12) "b1.0.changed"
    [1]=>
    int(1)
  }
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  array(2) {
    [0]=>
    string(12) "b1.0.changed"
    [1]=>
    string(12) "b1.1.changed"
  }
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  array(2) {
    [0]=>
    string(12) "b1.0.changed"
    [1]=>
    string(12) "b1.1.changed"
  }
  [2]=>
  string(10) "b2.changed"
}


--- Nested array references 2 elements in containing array:
array(3) {
  [0]=>
  &int(1)
  [1]=>
  &int(1)
  [2]=>
  &array(2) {
    [0]=>
    &int(1)
    [1]=>
    &int(1)
  }
}
string(12) "930101920101"
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    string(12) "b2.0.changed"
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    string(12) "b2.0.changed"
    [1]=>
    string(12) "b2.1.changed"
  }
}


--- Containing array references 1 element in nested array:
array(3) {
  [0]=>
  int(1)
  [1]=>
  &int(1)
  [2]=>
  &array(2) {
    [0]=>
    &int(1)
    [1]=>
    int(1)
  }
}
string(12) "930101920101"
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    string(12) "b2.0.changed"
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    string(12) "b2.0.changed"
    [1]=>
    string(12) "b2.1.changed"
  }
}


--- Containing array references 2 elements in nested array:
array(3) {
  [0]=>
  &int(1)
  [1]=>
  &int(1)
  [2]=>
  &array(2) {
    [0]=>
    &int(1)
    [1]=>
    &int(1)
  }
}
string(12) "930101920101"
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    string(12) "b2.0.changed"
    [1]=>
    int(1)
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    string(12) "b2.0.changed"
    [1]=>
    string(12) "b2.1.changed"
  }
}


--- Nested array references container:
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(1)
  [2]=>
  &array(2) {
    [0]=>
    int(1)
    [1]=>
    &array(3) {
      [0]=>
      int(1)
      [1]=>
      int(1)
      [2]=>
      *RECURSION*
    }
  }
}
string(28) "93010192019301019201930101c0"
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  int(1)
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    array(3) {
      [0]=>
      int(1)
      [1]=>
      int(1)
      [2]=>
      array(2) {
        [0]=>
        int(1)
        [1]=>
        array(3) {
          [0]=>
          int(1)
          [1]=>
          int(1)
          [2]=>
          NULL
        }
      }
    }
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    int(1)
    [1]=>
    array(3) {
      [0]=>
      int(1)
      [1]=>
      int(1)
      [2]=>
      array(2) {
        [0]=>
        int(1)
        [1]=>
        array(3) {
          [0]=>
          int(1)
          [1]=>
          int(1)
          [2]=>
          NULL
        }
      }
    }
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    string(12) "b2.0.changed"
    [1]=>
    array(3) {
      [0]=>
      int(1)
      [1]=>
      int(1)
      [2]=>
      array(2) {
        [0]=>
        int(1)
        [1]=>
        array(3) {
          [0]=>
          int(1)
          [1]=>
          int(1)
          [2]=>
          NULL
        }
      }
    }
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(2) {
    [0]=>
    string(12) "b2.0.changed"
    [1]=>
    string(12) "b2.1.changed"
  }
}
