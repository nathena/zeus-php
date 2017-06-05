<?php
function parms($string,$data) {
    $indexed=$data==array_values($data);
    foreach($data as $k=>$v) {
        if(is_string($v)) $v="'$v'";
        if($indexed) $string=preg_replace('/\?/',$v,$string,1);
        else $string=str_replace(":$k",$v,$string);
    }
    return $string;
}

//    Index Parameters
$string='INSERT INTO stuff(name,value) VALUES (?,?)';
$data=array('Fred',23);
print parms($string,$data);
echo "\r\n";
//    Named Parameters
$string='INSERT INTO stuff(name,value) VALUES (:name,:value)';
$data=array('name'=>'Fred','value'=>23);

print parms($string,$data);
