<?php

class Cards{
 
    public function hand($cards){
        /*Put a possible valid hands to make stairs*/
        $manos = array(
            "910111213" =>  "Stair",
            "234514"    =>  "Stair",
            "257891011" =>  "Stair",
        );
        return (array_key_exists($cards,$manos)) ? true : false;
    }
 
    public function validateIsStair($cards = null){
        if(!is_null($cards)) {

            if(is_array($cards)){

                /*valid the dimension of the array*/
                if(count($cards) <= 7 && count($cards >= 5)) {
                    /*valid if a card is equal to 1 */
                    if(!in_array(1, $cards)) {
                        /*valid if a card is greater than 14 */
                        foreach ($cards as $key) {
                            if($key > 14) {
                                return false;
                            }
                        }

                        asort($cards);
                        $b = implode("",$cards);
                        return $this->hand($b);

                    } else {
                       return false;
                    }
                    
                } else {
                    return false;
                }
                
            }else{
                return false;
            }

        } else return false;
        
    }
 
}
 
?>