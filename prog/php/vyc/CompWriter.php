<?php

namespace vy;

class CompWriter {
   
   /// kiíró
   protected $stream;
   /// Típus lekérdezés
   protected $map;
   /// reprezentációk
   protected $reprs;
   /// a kiírandó elem
   protected $item;
   
   function __construct() {
      $this->map = [];
      $this->reprs = [];
   }

   function typeMap() { return $this->map; }

   function setTypeMap( array $map ) { $this->map = $map; }

   /// a kimeneti fájl neve
   protected function filename() {
      return $this->stream->filename();
   }

   /// reprezentációk beállítása
   function setReprs( $r ) { 
      if ( is_array( $r ))
         $this->reprs = $r;
      else if ( $r )
         $this->reprs = [$r];
      else $this->reprs = [];
   }

   /// objektum kiírása fájlba
   function writeFile( $obj, $fname ) {
      $this->stream = new OStream( $fname );
      $this->writeObj( $obj );
   }
   
   protected function write($x) {
      $this->stream->write($x);
   }
   
   protected function writel() {
      call_user_func_array( [$this->stream,"writel"], func_get_args() );
   }
   
   /// megjegyzés kiírása
   protected function writeCmt($x) {
      if ( ! is_array($x))
         $x = [$x];
      foreach ($x as $r)
         $this->writel("// %s", $r);
   }
   
   /// itt definiált típus
   protected function own( $name ) {
      if ( $t = Tools::g( $this->map, $name ))
         return "*" == substr( $t, 0, 1 );
      return false;
   }
   
   /// egy típus reprezentációja
   protected function repr( $name, $check = true ) {
      $t = "".Tools::g( $this->map, $name );
      if ( "*" == substr( $t, 0, 1 ))
         $t = substr( $t, 1 );
      if ( ! $t )
         $t = $name;   
      $ret = $this->getRepr($t);
      if ( $check && ! $ret )
         throw new EVy("No representation for $name");
      return $ret;
   }

   /// egy reprezentáció
   protected function getRepr( $name ) {
      $rs = $this->reprs;
      for ($i = count($rs)-1; 0 <= $i; --$i) {
         if ( $ret = $rs[$i]->get($name))
            return $ret;
      }
      return null;
   }
   
   /// objektum kiírása
   protected function writeObj( $obj ) {
      if ( $obj instanceof Item ) {
         $this->item = $obj;
         $this->writeItem();
      } else
         throw new EVy("Cannot write $c");
   }      
   
   /// osztály kiírása
   protected function writeItem() {
      throw new EVy("Cannot write ".get_class($this->item));
   }

   /// blokk kezdete vagy vége
   protected function writeBlock($open) {
      if ( $open ) {
         $this->write(" {\n");
         $this->stream->indent(true);
      } else {
         $this->write("\n");
         $this->stream->indent(false);
         $this->writel("}");
      }
   }
   
   
}
