--TEST--
Object serialization / unserialization with inherited and hidden properties.
--FILE--
<?php

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
    $ser = msgpacki_serialize($obj);
    var_dump(bin2hex($ser));

    echo "Unserialized:\n";
    $uobj = msgpacki_unserialize($ser);
    var_dump($uobj);

    echo "Sanity check: ";
    var_dump($uobj->audit());
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
object(A)#%d (3) {
  [%r"?APriv"?:("A":)?private"?%r]=>
  string(7) "A.APriv"
  [%r"?AProt"?:protected"?%r]=>
  string(7) "A.AProt"
  ["APub"]=>
  string(6) "A.APub"
}
Serialized form:
string(100) "84c0a141a80041004150726976a7412e4150726976a8002a004150726f74a7412e4150726f74a441507562a6412e41507562"
Unserialized:
object(A)#%d (3) {
  [%r"?APriv"?:("A":)?private"?%r]=>
  string(7) "A.APriv"
  [%r"?AProt"?:protected"?%r]=>
  string(7) "A.AProt"
  ["APub"]=>
  string(6) "A.APub"
}
Sanity check: bool(true)


-- Test instance of B --


Before serialization:
object(B)#%d (6) {
  [%r"?BPriv"?:("B":)?private"?%r]=>
  string(7) "B.BPriv"
  [%r"?BProt"?:protected"?%r]=>
  string(7) "B.BProt"
  ["BPub"]=>
  string(6) "B.BPub"
  [%r"?APriv"?:("A":)?private"?%r]=>
  string(7) "A.APriv"
  [%r"?AProt"?:protected"?%r]=>
  string(7) "A.AProt"
  ["APub"]=>
  string(6) "A.APub"
}
Serialized form:
string(192) "87c0a142a80042004250726976a7422e4250726976a8002a004250726f74a7422e4250726f74a442507562a6422e42507562a80041004150726976a7412e4150726976a8002a004150726f74a7412e4150726f74a441507562a6412e41507562"
Unserialized:
object(B)#%d (6) {
  [%r"?BPriv"?:("B":)?private"?%r]=>
  string(7) "B.BPriv"
  [%r"?BProt"?:protected"?%r]=>
  string(7) "B.BProt"
  ["BPub"]=>
  string(6) "B.BPub"
  [%r"?APriv"?:("A":)?private"?%r]=>
  string(7) "A.APriv"
  [%r"?AProt"?:protected"?%r]=>
  string(7) "A.AProt"
  ["APub"]=>
  string(6) "A.APub"
}
Sanity check: bool(true)


-- Test instance of C --


Before serialization:
object(C)#%d (10) {
  [%r"?APriv"?:("C":)?private"?%r]=>
  string(7) "C.APriv"
  [%r"?AProt"?:protected"?%r]=>
  string(7) "C.AProt"
  ["APub"]=>
  string(6) "C.APub"
  [%r"?CPriv"?:("C":)?private"?%r]=>
  string(7) "C.CPriv"
  [%r"?CProt"?:protected"?%r]=>
  string(7) "C.BProt"
  ["CPub"]=>
  string(6) "C.CPub"
  [%r"?BPriv"?:("B":)?private"?%r]=>
  string(7) "B.BPriv"
  [%r"?BProt"?:protected"?%r]=>
  string(7) "B.BProt"
  ["BPub"]=>
  string(6) "B.BPub"
  [%r"?APriv"?:("A":)?private"?%r]=>
  string(7) "A.APriv"
}
Serialized form:
string(318) "8bc0a143a80043004150726976a7432e4150726976a8002a004150726f74a7432e4150726f74a441507562a6432e41507562a80043004350726976a7432e4350726976a8002a004350726f74a7432e4250726f74a443507562a6432e43507562a80042004250726976a7422e4250726976a8002a004250726f74a7422e4250726f74a442507562a6422e42507562a80041004150726976a7412e4150726976"
Unserialized:
object(C)#%d (10) {
  [%r"?APriv"?:("C":)?private"?%r]=>
  string(7) "C.APriv"
  [%r"?AProt"?:protected"?%r]=>
  string(7) "C.AProt"
  ["APub"]=>
  string(6) "C.APub"
  [%r"?CPriv"?:("C":)?private"?%r]=>
  string(7) "C.CPriv"
  [%r"?CProt"?:protected"?%r]=>
  string(7) "C.BProt"
  ["CPub"]=>
  string(6) "C.CPub"
  [%r"?BPriv"?:("B":)?private"?%r]=>
  string(7) "B.BPriv"
  [%r"?BProt"?:protected"?%r]=>
  string(7) "B.BProt"
  ["BPub"]=>
  string(6) "B.BPub"
  [%r"?APriv"?:("A":)?private"?%r]=>
  string(7) "A.APriv"
}
Sanity check: bool(true)
Done