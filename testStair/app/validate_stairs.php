<?php

class Cards{
 
    public function hand($cards){
        $manos = array(
            "910111213" =>  "Stair",
            "234514"    =>  "Stair",
            "257891011" =>  "Stair",
        );
        return (array_key_exists($cards,$manos)) ? true : false;
    }
 
    public function validateIsStair($cards){
        if(is_array($cards)){
            asort($cards);
            $b = implode("",$cards);
            return $this->hand($b);
        }else{
            return false;
        }
    }
 
}
 
$c = new Cards();
 
$plays = [
    [9,10,11,12,13],
    [14,2,3,4,5],
    [7,7,12,11,3,4,14],
    [7,8,12,13,14]
];
 
foreach($plays as $play){
    if( $c->validateIsStair($play) ) {
        echo "Is Stair \n";
    } else {
       echo "Is invalid hand \n";
    }
}

?>