<?php
class a {
    public function c(b $b){
        $b->c();
    }
}

class b {
    public function c(){
        echo get_class($this);
    }
}

class bb extends b{

}

$a = new a();
$b = new bb();

$a->c($b);