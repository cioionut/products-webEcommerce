<?php

define('S',33);
define('U',65);
define('L',97);

class My_Class_Cript{

    private $key = "CIO";

    public function __construct($key = NULL){
        if($key) $this->key = $key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function cript($input){
        $vect = array();
        $k = 0;
        for($j=0;$j<strlen($input);$j++,$k++){
            if($k >= strlen($this->key)) $k = 0;
            $i = ord($input[$j]);
            $c = ord($this->key[$k]);
            $x = 2*((int)($i/($c+1)))+3;
            $y = 3*($i%($c+1))+2;
            $dx=$dy='';
            switch (strlen($x)){
                case 1: $dx = U + ($c%10);break;
                case 2: $dx = S + ($c%10);break;
                case 3: $dx = L + ($c%10);break;
            }
            switch (strlen($y)){
                case 1: $dy = L + ($c%10);break;
                case 2: $dy = U + ($c%10);break;
                case 3: $dy = S + ($c%10);break;
            }
            $vect[]=chr($dx);
            $vect[]=$x;
            $vect[]=chr($dy);
            $vect[]=$y;
        }
        return implode($vect);
    }


    public function decript($input){
        $vect = array();
        $k = 0;
        $j = 0;
        while( $j < strlen($input) ){
            if($k >= strlen($this->key)) $k = 0;
            $dx = ord($input[$j++]);
            if($dx >= S && $dx < U) $dx = 2;
            elseif ($dx >= U && $dx < L) $dx = 1;
            else $dx = 3;
            $x = 0;
            for( $l = 0;$l < $dx; $l++,$j++){
                $x = $x*10 + $input[$j];
            }
            $x = ($x-3)/2;
            $dy = ord($input[$j++]);
            if($dy >= S && $dy < U) $dy = 3;
            elseif ($dy >= U && $dy < L) $dy = 2;
            else $dy = 1;
            $y = 0;
            for( $l = 0;$l < $dy; $l++,$j++){
                $y = $y*10 + $input[$j];
            }
            $y = ($y-2)/3;
            $c = ord($this->key[$k++]);
            $i = chr($x*($c+1)+$y);
            $vect[] = $i;
        }
        return implode($vect);
    }

}