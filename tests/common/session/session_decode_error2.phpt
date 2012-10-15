--TEST--
Test session_decode() function : error functionality
--SKIPIF--
<?php
if (!extension_loaded("session")) {
    echo "skip needs session enabled";
}
?>
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    die("skip this test is for PHP 5.3 or newer");
}
?>
--INI--
session.serialize_handler=msgpacki
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

ob_start();

echo "*** Testing session_decode() : error functionality ***\n";
$data = '83a3666f6f83000101020203a46775666682c0010002a4626c616882c0010002';

var_dump(session_start());
for($index = 0; $index < strlen($data); $index++) {
    echo "\n-- Iteration $index --\n";
    $encoded = pack('H*', substr($data, 0, $index));
    var_dump(session_decode($encoded));
    var_dump($_SESSION);
};

var_dump(session_destroy());
echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_decode() : error functionality ***
bool(true)

-- Iteration 0 --
bool(true)
array(0) {
}

-- Iteration 1 --
bool(true)
array(0) {
}

-- Iteration 2 --
bool(true)
array(0) {
}

-- Iteration 3 --
bool(true)
array(0) {
}

-- Iteration 4 --
bool(true)
array(0) {
}

-- Iteration 5 --
bool(true)
array(0) {
}

-- Iteration 6 --
bool(true)
array(0) {
}

-- Iteration 7 --
bool(true)
array(0) {
}

-- Iteration 8 --
bool(true)
array(0) {
}

-- Iteration 9 --
bool(true)
array(0) {
}

-- Iteration 10 --
bool(true)
array(0) {
}

-- Iteration 11 --
bool(true)
array(0) {
}

-- Iteration 12 --
bool(true)
array(0) {
}

-- Iteration 13 --
bool(true)
array(0) {
}

-- Iteration 14 --
bool(true)
array(0) {
}

-- Iteration 15 --
bool(true)
array(0) {
}

-- Iteration 16 --
bool(true)
array(0) {
}

-- Iteration 17 --
bool(true)
array(0) {
}

-- Iteration 18 --
bool(true)
array(0) {
}

-- Iteration 19 --
bool(true)
array(0) {
}

-- Iteration 20 --
bool(true)
array(0) {
}

-- Iteration 21 --
bool(true)
array(0) {
}

-- Iteration 22 --
bool(true)
array(0) {
}

-- Iteration 23 --
bool(true)
array(0) {
}

-- Iteration 24 --
bool(true)
array(0) {
}

-- Iteration 25 --
bool(true)
array(0) {
}

-- Iteration 26 --
bool(true)
array(0) {
}

-- Iteration 27 --
bool(true)
array(0) {
}

-- Iteration 28 --
bool(true)
array(0) {
}

-- Iteration 29 --
bool(true)
array(0) {
}

-- Iteration 30 --
bool(true)
array(0) {
}

-- Iteration 31 --
bool(true)
array(0) {
}

-- Iteration 32 --
bool(true)
array(0) {
}

-- Iteration 33 --
bool(true)
array(0) {
}

-- Iteration 34 --
bool(true)
array(0) {
}

-- Iteration 35 --
bool(true)
array(0) {
}

-- Iteration 36 --
bool(true)
array(0) {
}

-- Iteration 37 --
bool(true)
array(0) {
}

-- Iteration 38 --
bool(true)
array(0) {
}

-- Iteration 39 --
bool(true)
array(0) {
}

-- Iteration 40 --
bool(true)
array(0) {
}

-- Iteration 41 --
bool(true)
array(0) {
}

-- Iteration 42 --
bool(true)
array(0) {
}

-- Iteration 43 --
bool(true)
array(0) {
}

-- Iteration 44 --
bool(true)
array(0) {
}

-- Iteration 45 --
bool(true)
array(0) {
}

-- Iteration 46 --
bool(true)
array(0) {
}

-- Iteration 47 --
bool(true)
array(0) {
}

-- Iteration 48 --
bool(true)
array(0) {
}

-- Iteration 49 --
bool(true)
array(0) {
}

-- Iteration 50 --
bool(true)
array(0) {
}

-- Iteration 51 --
bool(true)
array(0) {
}

-- Iteration 52 --
bool(true)
array(0) {
}

-- Iteration 53 --
bool(true)
array(0) {
}

-- Iteration 54 --
bool(true)
array(0) {
}

-- Iteration 55 --
bool(true)
array(3) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["guff"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["blah"]=>
  array(0) {
  }
}

-- Iteration 56 --
bool(true)
array(3) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["guff"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["blah"]=>
  array(0) {
  }
}

-- Iteration 57 --
bool(true)
array(3) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["guff"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["blah"]=>
  array(0) {
  }
}

-- Iteration 58 --
bool(true)
array(3) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["guff"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["blah"]=>
  array(0) {
  }
}

-- Iteration 59 --
bool(true)
array(3) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["guff"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["blah"]=>
  array(0) {
  }
}

-- Iteration 60 --
bool(true)
array(3) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["guff"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["blah"]=>
  array(0) {
  }
}

-- Iteration 61 --
bool(true)
array(3) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["guff"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["blah"]=>
  array(0) {
  }
}

-- Iteration 62 --
bool(true)
array(3) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["guff"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["blah"]=>
  array(0) {
  }
}

-- Iteration 63 --
bool(true)
array(3) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["guff"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["blah"]=>
  &array(3) {
    ["foo"]=>
    &array(3) {
      [0]=>
      int(1)
      [1]=>
      int(2)
      [2]=>
      int(3)
    }
    ["guff"]=>
    &array(3) {
      [0]=>
      int(1)
      [1]=>
      int(2)
      [2]=>
      int(3)
    }
    ["blah"]=>
    *RECURSION*
  }
}
bool(true)
Done
