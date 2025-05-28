<?php

namespace vy;

class Color {

   static function parse( string $s ) {
      if (! preg_match(':^#[0-9a-fA-F]+:', $s, $m ))
         throw new EVy("Unknown color: $s");
      switch (strlen($s)) {
         case 4: return new Color( hexdec($s[1].$s[1]),
            hexdec($s[2].$s[2]), hexdec( $s[3].$s[3] ));
         case 5: return new Color( hexdec($s[1].$s[1]),
            hexdec($s[2].$s[2]), hexdec( $s[3].$s[3] ),
            hexdec($s[4].$s[4]));
         case 7: return new Color( hexdec(substr($s,1,2)),
            hexdec(substr($s,3,2)), hexdec(substr($s,5,2)));
         case 9: return new Color( hexdec(substr($s,1,2)),
            hexdec(substr($s,3,2)), hexdec(substr($s,5,2)),
            hexdec(substr($s,7,2)));
      }
      throw new EVy("Unknown color: $s");
   }

   public $r, $g, $b, $a;

   function __construct($r,$g,$b,$a=0xff) {
      $this->r = $r;
      $this->g = $g;
      $this->b = $b;
      $this->a = $a;
   }

   function equals( $o ) {
      if ( ! is_object($o) ) return false;
      if ( ! ($o instanceof Color)) return false;
      return $this->r == $o->r && $this->g == $o->g 
         && $this->b == $o->b && $this->a == $o->a;
   }

   function __toString() {
      $ret = sprintf("#%02x%02x%02x",$this->r, $this->g, $this->b );
      if ( 0xff != $this->a )
         $ret .= sprintf("%02x",$this->a);
      return $ret;
   }

}

