<?php

namespace vy;

/// get és set műveletek konfig értékekhez
class Configable {

   const
      NONE = "none",
      ANY = "any",
      ARRAY = "array",
      SCALAR = "scalar";
   
   /// a konfig értékek
   protected $conf;
   
   function __construct() {
      $this->conf = [];
   }
   
   /// konfig érték olvasása
   function get( $fld ) {
      $this->checkConf( $fld, self::NONE );
      return Tools::g( $this->conf, $fld );
   }
   
   /// konfig érték írása
   function set( $fld, $val=true ) {
      if ( is_array( $fld )) {
         if ( Tools::isAssoc( $fld )) {
            foreach ( $fld as $k=>$v)
               $this->set( $k, $v );
         } else {
            foreach ( $fld as $k )
               $this->set( $k, $val );
         }
         return;
      }
      $this->checkConf( $fld, $this->valKind($val) );
      $this->setConf( $fld, $val, false );
   }

   /// konfig érték bővítése
   function addConf( $fld, $val ) {
      $this->checkConf( $fld, self::ARRAY );
      $this->setConf( $fld, $val, true );
   }

   /// változó fajtája
   protected function confKind( $fld ) {
      return self::ANY;
   }
   
   /// változó fajtája
   protected function valKind( $v ) {
      if ( is_array( $v ) ) {
         if ( $v )
            return self::ARRAY;
         else
            return self::NONE;
      } else if ( $v )
         return self::SCALAR;
      else
         return self::NONE;
   }
   
   /// változó érvényesség ellenőrzés
   protected function checkConf( $fld, $kind ) {
      switch ( $fk = $this->confKind( $fld ) ) {
         case self::NONE:
            throw new EVy("Unknown field: $fld");
         case self::ANY:
            return;
      }
      if ( self::ARRAY == $fk && self::SCALAR == $fk )
         throw new EVy("Can only be scalar value: $fld");
   }
   
   /// érték beállítása
   protected function setConf( $fld, $val, $add ) {
      $k = $this->confKind( $fld );
      $v = $this->get( $fld );
      if ( $add ) {
         if ( ! is_array( $val ))
            $val = [$val];
         if ( ! $v )
            $v = [];
         $v = array_merge( $v, $val );
      } else {
         if ( null === $val && self::ARRAY == $k )
            $v = [];
         else
            $v = $val;
      }
      $this->conf[ $fld ] = $v;
   }
   
}
