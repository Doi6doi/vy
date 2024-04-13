<?php

namespace vy;

class CWriter {

   /// kiíró
   protected $stream;
   /// Típus lekérdezés
   protected $map;

   function __construct() {
      $this->map = [];
   }

   /// objektum alapján c header kiírása
   function writeHeader( $obj, $fname ) {
      $this->stream = new OStream( $fname );
      try {
         if ( $obj instanceof Interf )
            $this->writeInterfHeader( $obj );
         else
            throw new EVy("Unknown object: ".get_class($obj));
      } finally {
         $this->stream->close();
      }
   }

   /// a modul neve
   protected function module() {
      return pathinfo( $this->filename(), PATHINFO_FILENAME );
   }

   /// a kimeneti fájl neve
   protected function filename() {
      return $this->stream->filename();
   }

   /// interfész c fejléce
   protected function writeInterfHeader( Interf $intf ) {
      $this->writeHeaderHead();
      $this->writeInterfTypes($intf);
      $this->writeInterfStruct($intf);
      $this->writeInterfArgs($intf,false);
      $this->writeInterfProvide($intf,false);
      $this->writeHeaderTail();
   }

   /// .h fájl fejléce
   protected function writeHeaderHead() {
      $hh = strtoupper( $this->module()."H" );
      $s = $this->stream;
      $s->writel( "#ifndef $hh" );
      $s->writel( "#define $hh" );
      $s->writel( "#include <vy.h>" );
      $s->writel();
   }

   /// .h fájl fejléce
   protected function writeHeaderTail() {
      $hh = strtoupper( $this->module()."H" );
      $s = $this->stream;
      $s->writel();
      $s->writel( "#endif // $hh");
   }

   /// típus leképezés egy interfésznél
   protected function getType( $intf, $type ) {
      $key = $intf->name().".".$type;
      if ( $val = Tools::g( $this->map, $key ))
         return $val;
         else return $type;
   }

   /// interfész típusok kiírása
   protected function writeInterfTypes( $intf ) {
      foreach ( $intf->types() as $t ) {
         $tn = $this->getType( $intf, $t->name() );
         if ( "&" != substr($tn,0,1))
            $this->stream->writel( "typedef struct %s * %s;\n", $tn, $tn );
      }
   }

   /// interfész struktúra kiírása
   protected function writeInterfStruct( $intf ) {
      $s = $this->stream;
      $in = $intf->name();
      $s->writel( "typedef struct %sFun {", $in );
      $s->indent(true);
      foreach ( $intf->consts() as $c )
         $this->writeInterfStructConst( $c );
      foreach ( $intf->funcs() as $f )
         $this->writeInterfStructFunc( $f );
      $s->indent(false);
      $s->writel( "} * %sFun;", $in);
   }

   /// interfész struktúra konstans kiírása
   protected function writeInterfStructConst( $f ) {
      $s = $this->stream;
      $in = $f->owner();
      $name = $f->name();
      $s->writeIndent();
      $t = $f->sign()->result();
      $s->write( $this->getType( $in, $t )." " );
      if ( "&" == substr($name,0,1) ) {
         $name = substr($name,1);
         $s->writef("(* %s)(", $name );
         switch ( $name ) {
            case "ascii": case "utf":
               $s->write("VyCStr, VySize");
            break;
            case "dec":
               $s->write("VyDec");
            break;
            default: throw new EVy("Unknown special constant: $name");
         }
      } else {
         $s->writef("(* %s)(", $name );
      }
      $s->write(");\n");
   }

   /// interfész struktúra függvény kiírása
   protected function writeInterfStructFunc( $f ) {
      $s = $this->stream;
      $in = $f->owner();
      $s->writeIndent();
      if ( $t = $f->sign()->result() )
         $s->write( $this->getType( $in, $t )." " );
         else $s->write( "void " );
      $s->writef("(* %s)(", $f->name() );
      $first = true;
      foreach ( $f->sign()->args() as $a ) {
         if ( $first )
            $first = false;
            else $s->write(", ");
         $s->writef( $this->getType($in, $a->type()) );
         if ( $n = $a->name() )
            $s->write(" $n");
      }
      $s->write(");\n");
   }

   /// interfész lekérő függvény
   protected function writeInterfArgs( $intf, $body ) {
      $s = $this->stream;
      $s->writel();
      $s->writel( "VyImplemArgs %sArgs();", $intf->name() );
      if ( $body )
         throw new EVy("nyf");
   }

   /// provide tesztelő függvény
   protected function writeInterfProvide( $intf, $body ) {
      $s = $this->stream;
      $s->writel();
      $s->writel( "void %sProvide();", $intf->name() );
      if ( $body )
         throw new EVy("nyf");
   }

}
