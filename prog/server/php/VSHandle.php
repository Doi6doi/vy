<?php

/// azonosítóval rendelkező objektum
class VSHandle {

   /// új azonosító képzése
   static function create( VSHandled $h, $value=null ) {
      $kind = $h->handleKind();
      if ( ! array_key_exists( $kind, self::$handles ))
         self::$handles[$kind] = [];
      if ( null === $value )
         $value = count( self::$handles[$kind] )+1;
      $ret = new VSHandle( $kind, $value );
      self::$handles[$kind][$value] = $h;
      return $ret;
   }

   /// fajta
   public $kind;
   /// érték
   public $value;
   /// eddigi azonosítók száma
   protected static $handles = [];

   function __construct( $kind, $value ) {
      $this->kind = $kind;
      $this->value = $value;
   }

   function __toString() {
      return sprintf("#%d/%d", $this->kind, $this->value );
   }

}

