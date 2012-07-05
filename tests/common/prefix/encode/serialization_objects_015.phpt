--TEST--
Object serialization / unserialization: properties reference containing object
--INI--
error_reporting = E_ALL & ~E_STRICT
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

function check(&$obj) {
    var_dump($obj);
    $ser = msgpacki_encode($obj);
    var_dump(bin2hex($ser));

    $uobj = msgpacki_decode($ser);
    var_dump($uobj);
    $uobj->a = "obj->a.changed";
    var_dump($uobj);
    $uobj->b = "obj->b.changed";
    var_dump($uobj);
    $uobj->c = "obj->c.changed";
    var_dump($uobj);
}

echo "\n\n--- a refs container:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = &$obj;
$obj->b = 1;
$obj->c = 1;
check($obj);

echo "\n\n--- a eqs container:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = $obj;
$obj->b = 1;
$obj->c = 1;
check($obj);

echo "\n\n--- a,b ref container:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = &$obj;
$obj->b = &$obj;
$obj->c = 1;
check($obj);

echo "\n\n--- a,b eq container:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = $obj;
$obj->b = $obj;
$obj->c = 1;
check($obj);

echo "\n\n--- a,b,c ref container:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = &$obj;
$obj->b = &$obj;
$obj->c = &$obj;
check($obj);

echo "\n\n--- a,b,c eq container:\n";
$ext = 1;
$obj = new stdClass;
$obj->a = $obj;
$obj->b = $obj;
$obj->c = $obj;
check($obj);

echo "Done";
?>
--EXPECTF--
--- a refs container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}
string(56) "83a16183a16183a161c0a16201a16301a16201a16301a16201a16301"
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      int(1)
      ["c"]=>
      int(1)
    }
    ["b"]=>
    int(1)
    ["c"]=>
    int(1)
  }
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      int(1)
      ["c"]=>
      int(1)
    }
    ["b"]=>
    int(1)
    ["c"]=>
    int(1)
  }
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      int(1)
      ["c"]=>
      int(1)
    }
    ["b"]=>
    int(1)
    ["c"]=>
    int(1)
  }
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      int(1)
      ["c"]=>
      int(1)
    }
    ["b"]=>
    int(1)
    ["c"]=>
    int(1)
  }
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}


--- a eqs container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}
string(56) "83a16183a16183a161c0a16201a16301a16201a16301a16201a16301"
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      int(1)
      ["c"]=>
      int(1)
    }
    ["b"]=>
    int(1)
    ["c"]=>
    int(1)
  }
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      int(1)
      ["c"]=>
      int(1)
    }
    ["b"]=>
    int(1)
    ["c"]=>
    int(1)
  }
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      int(1)
      ["c"]=>
      int(1)
    }
    ["b"]=>
    int(1)
    ["c"]=>
    int(1)
  }
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      int(1)
      ["c"]=>
      int(1)
    }
    ["b"]=>
    int(1)
    ["c"]=>
    int(1)
  }
  ["b"]=>
  int(1)
  ["c"]=>
  int(1)
}


--- a,b ref container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  int(1)
}
string(128) "83a16183a16183a161c0a162c0a16301a16283a161c0a162c0a16301a16301a16283a16183a161c0a162c0a16301a16283a161c0a162c0a16301a16301a16301"
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["c"]=>
  int(1)
}


--- a,b eq container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  int(1)
}
string(128) "83a16183a16183a161c0a162c0a16301a16283a161c0a162c0a16301a16301a16283a16183a161c0a162c0a16301a16283a161c0a162c0a16301a16301a16301"
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["c"]=>
  int(1)
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      int(1)
    }
    ["c"]=>
    int(1)
  }
  ["c"]=>
  int(1)
}


--- a,b,c ref container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  *RECURSION*
}
string(236) "83a16183a16183a161c0a162c0a163c0a16283a161c0a162c0a163c0a16383a161c0a162c0a163c0a16283a16183a161c0a162c0a163c0a16283a161c0a162c0a163c0a16383a161c0a162c0a163c0a16383a16183a161c0a162c0a163c0a16283a161c0a162c0a163c0a16383a161c0a162c0a163c0"
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["c"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["c"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["c"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["c"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
}


--- a,b,c eq container:
object(stdClass)#%d (3) {
  ["a"]=>
  *RECURSION*
  ["b"]=>
  *RECURSION*
  ["c"]=>
  *RECURSION*
}
string(236) "83a16183a16183a161c0a162c0a163c0a16283a161c0a162c0a163c0a16383a161c0a162c0a163c0a16283a16183a161c0a162c0a163c0a16283a161c0a162c0a163c0a16383a161c0a162c0a163c0a16383a16183a161c0a162c0a163c0a16283a161c0a162c0a163c0a16383a161c0a162c0a163c0"
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["c"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["c"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["c"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
}

Warning: Attempt to assign property of non-object in %s on line %d
array(3) {
  ["a"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["b"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
  ["c"]=>
  array(3) {
    ["a"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["b"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
    ["c"]=>
    array(3) {
      ["a"]=>
      NULL
      ["b"]=>
      NULL
      ["c"]=>
      NULL
    }
  }
}
Done
