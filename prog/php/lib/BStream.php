<?php

namespace vy;

/// vy bináris olvasó
class BStream {

   protected $data;
   protected $at;
   protected $filename;

   function __construct( $filename ) {
      $this->filename = $filename;
      $this->data = file_get_contents( $filename );
      if ( false === $this->data )
        throw new EVy("Could not load file '$filename'");
      $this->at=0;
   }

   function eos() {
      return $this->at >= strlen( $this->data );
   }

   function readByte() {
      return $this->data[$this->at++];
   }

   function readToken($tok) {
      $n = strlen($tok);
      $ret = $this->readN( $n );
      if ( $ret !== $tok )
         throw $this->unexp("'$tok'", $ret );
   }

   function readInt() {
      $b = $this->readByte();
      if ( "\x7c" == $b ) return 1;
      if ( "\x30" == $b ) return 0;
      if ( $b < "\x31" || "\x39" < $b )
         throw $this->unexp( "int", $b );
      $n = ord($b)-ord("\x30");
      return $this->readIntN($n);
   }

   function readIntN($n) {
      $d = $this->readN($n);
      $ret = 0;
      for ($i=0; $i<$n; ++$i) {
         $b = ord($d[$i]);
         if ( 0 == $i )
            $neg = 0x80 & $b;
         $ret = $ret << 8 | $b;
      }
      if ( $neg )
         $ret -= (1 << 8*$n);
      return $ret;
   }

   function readVer() {
      $this->readToken("\x40");
      return new Version( $this->readIntN(3) );
   }

   function readString() {
      switch ( $b = $this->readByte() ) {
         case "\x27":
         case "\x22":
            $n = $this->readInt();
            return $this->readN($n);
         break;
         default: throw $this->unexp("string",$b);
      }
   }

   function readN($n) {
      $ret = substr( $this->data, $this->at, $n );
      $this->at += strlen($ret);
      return $ret;
   }

   function readList() {
      $this->readToken("\x5b");
      $n = $this->readInt();
      $ret = [];
      for ( ; 0 <$n; --$n)
         $ret [] = $this->readAny();
      return $ret;
   }

   function unexp($exp,$fnd) {
      return new EVy("$exp expected but '$fnd' found");
   }

}
