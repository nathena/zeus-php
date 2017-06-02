<?php
class a {
    public function c(b $b){
        $b->c();
    }
}

class b {

    public function __construct($data)
    {
        print_r($data);
        echo 2;
    }

    public function c(){
        echo get_class($this);
    }
}

class bb extends b{

//    public function __construct()
//    {
//        echo 1;
//        parent::__construct(null);
//    }
}

$a = new a();
$b = new bb();

$a->c($b);