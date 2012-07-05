--TEST--
Problem in serialisation of circular references
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class a {
    public $b;
}
class b {
    public $c;
}
class c {
    public $d;
}
$a = new a();
$a->b = new b();
$a->b->c = new c();
$a->b->c->d = $a;
var_dump(unserialize(serialize($a)));
?>
--EXPECTF--
object(MessagePacki\a)#%d (1) {
  ["b"]=>
  object(MessagePacki\b)#%d (1) {
    ["c"]=>
    object(MessagePacki\c)#%d (1) {
      ["d"]=>
      *RECURSION*
    }
  }
}
