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
   
   /// objektum alapján c fájl kiírása
   function writeBody( $obj, $fname ) {
	  $this->stream = new OStream( $fname );
	  try {
		 if ( $obj instanceof Interf )
            $this->writeInterfBody( $obj );
		 else
		    throw new EVy("Unknown object: ".get_class($obj));
      } finally {
		  $this->stream->close();
	  }
   }

   /// típusmegfeleltetés beállítása
   function setTypeMap( array $map ) {
      $this->map = $map;
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
      $this->writeInterfArgs($intf);
      $this->writeHeaderTail();
   }

   /// interfész C törzse
   protected function writeInterfBody( Interf $intf ) {
	  $this->writeBodyHead();
	  $this->writeInterfStubs( $intf );
	  $this->writeBodyInit( $intf );
   }

   /// .c fájl fejléce
   protected function writeBodyHead() {
	  $s = $this->stream;
	  $s->writel( '#include <vy_implem.h>');
	  $s->writel( '#include "%s.h"', $this->module() );
	  $s->writel();
   }

   /// c fájl csonkok
   protected function writeInterfStubs( $intf ) {
      foreach ( $intf->consts() as $c )
         $this->writeInterfConst( $c, true );
      foreach ( $intf->funcs() as $f )
         $this->writeInterfFunc( $f, true );
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
   protected function getType( $intf, $type, $trim ) {
      $key = $intf->name().".".$type;
      if ( $val = Tools::g( $this->map, $key ))
         $ret = $val;
      else if ( $val = Tools::g( $this->map, $type ))
         $ret = $val;
      else
         $ret = $type;
      if ( $trim && "&" == substr( $ret, 0, 1 ))
         $ret = substr( $ret, 1 );
      return $ret;
   }

   /// interfész típusok kiírása
   protected function writeInterfTypes( $intf ) {
      foreach ( $intf->types() as $t ) {
         $tn = $this->getType( $intf, $t->name(), false );
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
         $this->writeInterfConst( $c, false );
      foreach ( $intf->funcs() as $f )
         $this->writeInterfFunc( $f, false );
      $s->indent(false);
      $s->writel( "} %sFun;", $in);
   }

   /// interfész struktúra konstans kiírása
   protected function writeInterfConst( $f, $stub ) {
      $s = $this->stream;
      $in = $f->owner();
      $name = $f->name();
      if ( $stub )
         $s->write( "static " );
         else $s->writeIndent();
      $t = $f->sign()->result();
      $s->write( $this->getType( $in, $t, true )." " );
      $fn = $this->funcName( $f, $stub ? $in : null  );
      if ( $stub )
	     $s->writef( "vy%s(", $fn );
	     else $s->writef("(* %s)(", $fn );
      if ( "&" == substr($name,0,1) ) {
         switch ( $name ) {
            case "&ascii": case "&utf": case "&hex":
               $s->write("VyCStr, VySize");
            break;
            case "&dec":
               $s->write("VyDec");
            break;
            default: throw new EVy("Unknown special constant: $name");
         }
      }
      if ( $stub ) {
		 $this->writeThrowStub( $fn );
      } else {
		$s->writel(");");
      }
   }

   /// stub kivétel
   protected function writeThrowStub( $name ) {
	  $s = $this->stream;
	  $s->writel(" ) {");
	  $s->indent(true);
      $s->writel("vyThrow(\"stub %s\");", $name );
	  $s->indent(false);
	  $s->writel("}\n");
   }
	   
   /// interfész struktúra függvény kiírása
   protected function writeInterfFunc( $f, $stub ) {
      $s = $this->stream;
      $in = $f->owner();
      $fn = $this->funcName( $f, $stub ? $in : null );
      if ( $stub )
         $s->write( "static " );
         else $s->writeIndent();
      if ( $t = $f->sign()->result() )
         $s->write( $this->getType( $in, $t, true )." " );
         else $s->write( "void " );
      if ( $stub )
         $s->writef( "vy%s(", $fn );
         else $s->writef("(* %s)(", $fn );
      $first = true;
      foreach ( $f->sign()->args() as $a ) {
         if ( $first )
            $first = false;
            else $s->write(", ");
         $s->writef( $this->getType($in, $a->type(), true ) );
         if ( $n = $a->name() )
            $s->write(" $n");
      }
      if ( $stub )
		 $this->writeThrowStub( $fn );
         else $s->writel(");");
   }

   /// interfész lekérő függvény
   protected function writeInterfArgs( $intf ) {
      $s = $this->stream;
      $s->writel();
      $s->writel( "#define VY%sARGS( name ) \\", strtoupper( $intf->name() ));
      $s->indent(true);
      $s->writel( "VyImplemArgs name = vyImplemArgs( \"%s.%s\", vyVer(%s)); \\",
         $intf->pkg(), $intf->name(), substr($intf->ver(),1) );
      foreach ( $intf->types() as $t ) {
         $tn = $this->getType( $intf, $t->name(), false );
         if ( "&" == substr($tn,0,1))
            $re = sprintf("vyNative(\"%s\")", substr($tn,1));
            else $re = "NULL";
         $s->writel( "vyImplemArgsType( name, \"%s\", %s ); \\",
            $t->name(), $re );
      }
      foreach ( $intf->consts() as $c ) {
         $s->writel( "vyImplemArgsFunc( name, \"%s\"); \\",
            $this->funcName( $c ));
      }
      foreach ( $intf->funcs() as $f ) {
         $s->writel( "vyImplemArgsFunc( name, \"%s\"); \\",
            $f->name() );
      }
      $s->indent(false);
      $s->writel();
   }

   /// konstans esetén const kerül elé
   protected function funcName( $f, $intf = null ) {
      $name = $f->name();
      if ( $f->cons() && '&' == $name[0] )
         $name = "const".strtoupper( $name[1] ).substr( $name,2 );
      if ( $intf )
         $name = $intf->name().Tools::firstUpper($name);      
      return $name;
   }

   /// inicializáló kód
   protected function writeBodyInit( $intf ) {
	  $s = $this->stream;
	  $name = $intf->name();
	  $s->writel( "void vyInit%s( VyContext ctx ) {", $name );
	  $s->indent(true);
	  $s->writel( "VY%sARGS( args );", strtoupper($name) );
	  foreach ($intf->consts() as $c)
	     $this->writeBodyInitFunc( $c );
	  foreach ($intf->funcs() as $f)
	     $this->writeBodyInitFunc( $f );
	  $s->writel( "vyAddImplem( ctx, args );" );
	  $s->indent(false);
	  $s->writel("}\n");
   }	     
   
   protected function writeBodyInitFunc( $func ) {
      $fn = $this->funcName( $func );
	  $ffn = $this->funcName( $func, $func->owner() );
	  $this->stream->writel( 'vyImplemArgsImpl( args, "%s", &vy%s );', $fn, $ffn );
   }

}
