<?php

namespace vy;

/// vy verzió
class Version {

   /// olvasás Stream-ből
   static function read( Stream $s, $withCond = false ) {
      $s->readToken("@");
      $cond = $withCond ? self::readCond( $s ) : null;
      $num = $s->readNat();
      return new Version( $num, $cond );
   }
   
   /// feltétel olvasása Stream-ből
   static function readCond( Stream $s ) {
      $ret = "";
      while ( in_array( $s->next(), ["<","=",">"] ))
         $ret .= $s->read();
      if ( ! $ret )
         throw $s->notexp( "version condition" );
      return $ret;
   }

   // feltétel parse-olása
   static function parse( $s, $force = false ) {
      if ( preg_match( '#^@([<=>]*)([0-9]+)$#', $s, $m ))
         return new Version( $m[2], $m[1] );
      else if ( $force )
         throw new EVy("Unknown version: $s");
      else
         return false;
   }

   /// $v jobb-e a feltételhez, mint $old
   static function better( Version $v, Version $cond, $old ) {
      if ( ! $v->matches($cond) ) return false;
      if ( ! $old ) return true;
      return $v->day() > $old->day();
   }      

   /// mai napig feltétel
   static function untilNow() {
      return self::parse( "@<=".date("Ymd") );
   }
   
   protected $num;
   protected $rel;

   function __construct( $num, $rel = null ) {
      $this->num = $num;
      $this->rel = $rel ? $rel : null;
      $this->check();
   }
   
   function num() { return $this->num; }
   
   function rel() { return $this->rel; }
   
   /// averzió napja
   function day() {
      $n = $this->num;
      switch ( strlen( $n )) {
         case 2: return "20".$n."0101";
         case 4: return "20".$n."01";
         case 6: return $n."01";
         case 8: return $n;
         default: throw new EVy("Unknown version number: $n");
      }
   }

   /// illeszkedik-e a feltételre
   function matches( Version $cond ) {
      $d = $this->day();
      $cd = $cond->day();
      switch ( $cond->rel ) {
         case "<": return $d < $cd;
         case "<=": return $d <= $cd;
         case "=": return $d = $cd;
         case ">=": return $d >= $cd;
         case ">": return $d > $cd;
         null: throw new EVy("Condition $cond does not have rel");
      }
   }
   
   function __toString() {
      return sprintf( "@%s%s", $this->rel, $this->num );
   }
   
   /// létrehozó adatok ellenőrzése
   protected function check() {
      $d = $this->day();
      $s = sprintf("%s-%s-%s", substr($d,0,4), 
         substr($d,4,2), substr($d,6,2));
      if ( ! strtotime( $s ))
         throw new EVy("Invalid number: ".$this->num);
      switch ( $this->rel ) {
         case null: case "<": case "<=": case "=": case ">=": case ">":
            break;
         default: throw new EVy("Invalid rel: ".$this->rel);
      }
   }
}
