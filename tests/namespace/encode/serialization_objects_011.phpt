--TEST--
Object serialization / unserialization with inherited and hidden properties.
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

Class A {
    private $APriv = "A.APriv";
    protected $AProt = "A.AProt";
    public $APub = "A.APub";

    function audit() {
        return isset($this->APriv, $this->AProt, $this->APub);
    }
}

Class B extends A {
    private $BPriv = "B.BPriv";
    protected $BProt = "B.BProt";
    public $BPub = "B.BPub";

    function audit() {
        return  parent::audit() && isset($this->AProt, $this->APub,
                     $this->BPriv, $this->BProt, $this->BPub);
    }
}

Class C extends B {
    private $APriv = "C.APriv";
    protected $AProt = "C.AProt";
    public $APub = "C.APub";

    private $CPriv = "C.CPriv";
    protected $CProt = "C.BProt";
    public $CPub = "C.CPub";

    function audit() {
        return parent::audit() && isset($this->APriv, $this->AProt, $this->APub,
                     $this->BProt, $this->BPub,
                     $this->CPriv, $this->CProt, $this->CPub);
    }
}

function prettyPrint($obj) {
    echo "\n\nBefore serialization:\n";
    var_dump($obj);

    echo "Serialized form:\n";
    $ser = encode($obj);
    var_dump(bin2hex($ser));

    echo "Unserialized:\n";
    $uobj = decode($ser);
    var_dump($uobj);

    echo "Sanity check: ";
    if (is_callable(array($uobj, 'audit'))) {
        var_dump($uobj->audit());
    } else {
        var_dump(null);
    }
}

echo "-- Test instance of A --\n";
prettyPrint(new A);
echo "\n\n-- Test instance of B --\n";
prettyPrint(new B);
echo "\n\n-- Test instance of C --\n";
prettyPrint(new C);

echo "Done";
?>
--EXPECTF--
-- Test instance of A --


Before serialization:
object(MessagePacki\A)#%d (3) {
  ["APriv":"MessagePacki\A":private]=>
  string(7) "A.APriv"
  ["AProt":protected]=>
  string(7) "A.AProt"
  ["APub"]=>
  string(6) "A.APub"
}
Serialized form:
string(82) "83a54150726976a7412e4150726976a54150726f74a7412e4150726f74a441507562a6412e41507562"
Unserialized:
array(3) {
  ["APriv"]=>
  string(7) "A.APriv"
  ["AProt"]=>
  string(7) "A.AProt"
  ["APub"]=>
  string(6) "A.APub"
}
Sanity check: NULL


-- Test instance of B --


Before serialization:
object(MessagePacki\B)#%d (6) {
  ["BPriv":"MessagePacki\B":private]=>
  string(7) "B.BPriv"
  ["BProt":protected]=>
  string(7) "B.BProt"
  ["BPub"]=>
  string(6) "B.BPub"
  ["APriv":"MessagePacki\A":private]=>
  string(7) "A.APriv"
  ["AProt":protected]=>
  string(7) "A.AProt"
  ["APub"]=>
  string(6) "A.APub"
}
Serialized form:
string(162) "86a54250726976a7422e4250726976a54250726f74a7422e4250726f74a442507562a6422e42507562a54150726976a7412e4150726976a54150726f74a7412e4150726f74a441507562a6412e41507562"
Unserialized:
array(6) {
  ["BPriv"]=>
  string(7) "B.BPriv"
  ["BProt"]=>
  string(7) "B.BProt"
  ["BPub"]=>
  string(6) "B.BPub"
  ["APriv"]=>
  string(7) "A.APriv"
  ["AProt"]=>
  string(7) "A.AProt"
  ["APub"]=>
  string(6) "A.APub"
}
Sanity check: NULL


-- Test instance of C --


Before serialization:
object(MessagePacki\C)#%d (10) {
  ["APriv":"MessagePacki\C":private]=>
  string(7) "C.APriv"
  ["AProt":protected]=>
  string(7) "C.AProt"
  ["APub"]=>
  string(6) "C.APub"
  ["CPriv":"MessagePacki\C":private]=>
  string(7) "C.CPriv"
  ["CProt":protected]=>
  string(7) "C.BProt"
  ["CPub"]=>
  string(6) "C.CPub"
  ["BPriv":"MessagePacki\B":private]=>
  string(7) "B.BPriv"
  ["BProt":protected]=>
  string(7) "B.BProt"
  ["BPub"]=>
  string(6) "B.BPub"
  ["APriv":"MessagePacki\A":private]=>
  string(7) "A.APriv"
}
Serialized form:
string(270) "8aa54150726976a7432e4150726976a54150726f74a7432e4150726f74a441507562a6432e41507562a54350726976a7432e4350726976a54350726f74a7432e4250726f74a443507562a6432e43507562a54250726976a7422e4250726976a54250726f74a7422e4250726f74a442507562a6422e42507562a54150726976a7412e4150726976"
Unserialized:
array(9) {
  ["APriv"]=>
  string(7) "A.APriv"
  ["AProt"]=>
  string(7) "C.AProt"
  ["APub"]=>
  string(6) "C.APub"
  ["CPriv"]=>
  string(7) "C.CPriv"
  ["CProt"]=>
  string(7) "C.BProt"
  ["CPub"]=>
  string(6) "C.CPub"
  ["BPriv"]=>
  string(7) "B.BPriv"
  ["BProt"]=>
  string(7) "B.BProt"
  ["BPub"]=>
  string(6) "B.BPub"
}
Sanity check: NULL
Done
