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
    $ser = encode($a);
    var_dump(bin2hex($ser));

    $b = decode($ser);
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
string(20) "939393c0010101010101"
array(3) {
  [0]=>
  array(3) {
    [0]=>
    array(3) {
      [0]=>
      NULL
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
string(44) "939393c0c00193c0c001019393c0c00193c0c0010101"
array(3) {
  [0]=>
  array(3) {
    [0]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      int(1)
    }
    [1]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      int(1)
    }
    [2]=>
    int(1)
  }
  [1]=>
  array(3) {
    [0]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      int(1)
    }
    [1]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      int(1)
    }
    [2]=>
    int(1)
  }
  [2]=>
  int(1)
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  array(3) {
    [0]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      int(1)
    }
    [1]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      int(1)
    }
    [2]=>
    int(1)
  }
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
string(80) "939393c0c0c093c0c0c093c0c0c09393c0c0c093c0c0c093c0c0c09393c0c0c093c0c0c093c0c0c0"
array(3) {
  [0]=>
  array(3) {
    [0]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [1]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [2]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
  }
  [1]=>
  array(3) {
    [0]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [1]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [2]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
  }
  [2]=>
  array(3) {
    [0]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [1]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [2]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  array(3) {
    [0]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [1]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [2]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
  }
  [2]=>
  array(3) {
    [0]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [1]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [2]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  array(3) {
    [0]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [1]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
    [2]=>
    array(3) {
      [0]=>
      NULL
      [1]=>
      NULL
      [2]=>
      NULL
    }
  }
}
array(3) {
  [0]=>
  string(10) "b0.changed"
  [1]=>
  string(10) "b1.changed"
  [2]=>
  string(10) "b2.changed"
}
Done
