<?php
class a {
    public function c(b $b){
        $b->c();
    }

    public function __call($m,$data){
        echo $m;
        print_r($data);
    }

}

class b {

    public function __construct($data)
    {
        echo get_class($this);
    }

    public function c(){
        echo get_class($this);
    }

    public function __set($key,$val){
        $this->data[$key] = $val;
    }

    public function setData($data){
        foreach($data as $key => $val ){
            $this->{$key} = $val;
        }
    }
}

class bb extends b{

    public function __set($key,$val){
        $this->data[$key] = $val;

        echo "========>",1;
    }
}

$a = new bb();
$a->c();
