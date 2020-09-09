<?php
trait observer{
    public function __construct()
    {
        if( !isset( $GLOBALS['hook'] ) && !is_array( $GLOBALS['hook'] ) )
        {
            return;
        }
    }
 
    public function watch($channel, $func){
        if( !isset( $GLOBALS['hook'][$channel] ) ){
            $GLOBALS['hook'][$channel] = array();   
        }
         
        array_push($GLOBALS['hook'][$channel], $func);
    }
     
    private function subscribe($channel){
        if( isset( $GLOBALS['hook'][$channel] ) ){
            foreach( $GLOBALS['hook'][$channel] as $func ){
                $func();
            }
        }
    }
}
?>