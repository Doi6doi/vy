<?php

namespace vy;

/// get és set műveletek konfig értékekhez
class Configable {
   
   /// a konfig értékek
   protected $conf;
   
   function __construct() {
      $this->conf = [];
   }
   
   /// konfig érték olvasása
   function get( $fld ) {
      return Tools::g( $this->conf, $fld );
   }
   
   /// konfig érték írása
   function set( $fld, $val ) {
      $this->conf[$fld] = $val;
   }
   
}
