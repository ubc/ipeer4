<?php
 if (!function_exists('toBoolean')) {
    
     /**
      * Convert to boolean
      *
      * @param $booleable
      * @return boolean
      */
     function toBoolean($booleable)
     {
         return filter_var($booleable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
     }
 }
