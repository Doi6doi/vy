<?php

namespace vy;

class CWriter {

   /// kiíró
   protected $stream;
   /// Típus lekérdezés
   protected $map;
   /// Cast-ok
   protected $casts;

   const
      CAST = "cast",
      CASTL = "castl",
      FUN = "fun",
      REPR = "repr";

   function __construct() {
      $this->map = [];
      $this->casts = [];
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
   
   /// cast-ok beállítása
   function setCasts( array $casts ) {
	  $this->casts = $casts;
   }

   /// a modul neve
   protected function module() {
      return pathinfo( $this->filename(), PATHINFO_FILENAME );
   }

   /// a kimeneti fájl neve
   protected function filename() {
      return $this->stream->filename();
   }

   /// egy megnevezés
   protected function getName( $kind, $obj, $other=null ) {
	  switch ( $kind ) {
		 case self::CAST:
		    $on = $other ? $other->name() : "";
		    return sprintf( "%sCast%s", $on, $obj );
		 case self::CASTL:
		    return Tools::firstLower( $this->getName( self::CAST, $obj, $other ));
		 case self::FUN:
		    return $obj->name()."Fun";
		 case self::REPR:
		    return "vyr".$this->trim( $obj );
		 default:
		    throw new EVy("Unknown kind: $kind");
      }
  }
  

   /// interfész c fejléce
   protected function writeInterfHeader( Interf $intf ) {
      $this->writeHeaderHead();
      $this->writeTypes($intf);
      $this->writeInterfStruct($intf);
      $this->writeInterfArgs($intf);
      $this->writeInterfUtil($intf);
      $this->writeHeaderTail();
   }

   /// interfész C törzse
   protected function writeInterfBody( Interf $intf ) {
	  $this->writeBodyHead();
	  $this->writeBodyReprs( $intf );
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
	  $this->writeSetters( $intf, true );
      foreach ( $intf->consts() as $c )
         $this->writeConst( $c, true );
      foreach ( $intf->funcs() as $f )
         $this->writeFunc( $f, true );
      foreach ( $this->casts as $k => $v )
         $this->writeCast( $intf, $k, $v, true );
   }

   /// reprezentíációk
   protected function writeBodyReprs( $intf ) {
	  $s = $this->stream;
	  foreach ( $intf->types() as $t ) {
         $tn = $this->getType( $intf, $t->name(), false );
         $ch = substr( $tn, 0, 1 );
         switch ( $ch ) {
			case "&": break;
			case "^":
			   $s->writel( "extern VyRepr %s;\n", 
			      $this->getName( self::REPR, $tn ) );
			break;
			default:
			   $s->writel("struct %s {", $tn );
			   $s->indent(true);
			   $s->writel("VyRepr repr;");
			   $s->indent(false);
			   $s->writel("};\n");
               $s->writel( "VyRepr %s;\n", 
                  $this->getName( self::REPR, $tn ) );
               $s->writef( "void destroy%s( VyPtr", $tn );
               $this->writeThrowStub( "destroy$tn" );
         }
      }
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

   /// ^ vagy & levágása
   protected function trim( $s ) {
	  $ch = substr( $s, 0, 1 );
	  if ( in_array( $ch, ["^", "&"] ))
	     return substr( $s, 1 );
	  return $s;
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
      if ( $trim )
         return $this->trim( $ret );
         else return $ret;
   }

   /// interfész típusok kiírása
   protected function writeTypes( $intf ) {
	  $had = [];
      foreach ( $intf->types() as $t ) {
         $tn = $this->getType( $intf, $t->name(), false );
         $ch = substr( $tn, 0, 1 );
         if ( "&" != $ch ) {
			 $tn = $this->trim( $tn );
			 $this->writeType( $tn );
             $had [] = $tn;
	     }
      }
      foreach ( $this->casts as $k=>$v ) {
		 if ( ! in_array( $v, $had ))
		    $this->writeType( $v );
      }
   }

   /// egy típus kiírása
   protected function writeType( $t ) {
      $this->stream->writel( "typedef struct %s * %s;\n", $t, $t );
   }

   /// interfész struktúra kiírása
   protected function writeInterfStruct( $intf ) {
      $s = $this->stream;
      $nf = $this->getName( self::FUN, $intf );
      $s->writel( "typedef struct %s {", $nf );
      $s->indent(true);
      $this->writeSetters( $intf, false );
      foreach ( $this->casts as $k=>$v )
         $this->writeCast( $intf, $k, $v, false );
      foreach ( $intf->consts() as $c )
         $this->writeConst( $c, false );
      foreach ( $intf->funcs() as $f )
         $this->writeFunc( $f, false );
      $s->indent(false);
      $s->writel( "} %s;", $nf );
   }

   /// itt definiált típusok
   protected function ownTypes( $intf, $shor = true ) {
	  $ret = [];
      foreach ( $intf->types() as $t ) {
         $tn = $this->getType( $intf, $t->name(), false );
         $ch = substr( $tn, 0, 1 );
         if ( ! in_array( $ch, ["&","^"] ) )
            $ret [] = $tn;
      }
      if ( $shor && 1 == count($ret) )
         return [""];
      return $ret;
   }

   /// értékadó függvények
   protected function writeSetters( $intf, $stub ) {
	  $s = $this->stream;
	  $ots = $this->ownTypes( $intf, false );
	  $one = 1 == count( $ots );
      foreach ( $ots as $t ) {
   	     if ( $stub ) {
	 	    $n = sprintf( "%sSet%s", $intf->name(), $one ? "" : $t );
		    $s->writef( "static void vy%s( %s *, %s", $n, $t, $t );
		    $this->writeThrowStub( $n );
         } else {
		    $n = sprintf( "set%s", $one ? "" : $t );
		    $s->writel( "void (* %s)( %s *, %s );", $n, $t, $t );
         }
      }
   }

   /// interfész struktúra konstans kiírása
   protected function writeConst( $f, $stub ) {
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
		 $this->writeThrowStub( $in->name().Tools::firstUpper($fn) );
      } else {
		$s->write(");\n");
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
   protected function writeFunc( $f, $stub ) {
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
         else $s->write(");\n");
   }

   /// cast kiírása
   protected function writeCast( $intf, $t, $u, $stub ) {
      $s = $this->stream;
      if ( $stub )
         $s->writef( "static " );
         else $s->writeIndent();
      $s->write( "$u " );
      $cn = $this->getName( self::CAST, $u, $intf );
      $cl = $this->getName( self::CASTL, $u );
      if ( $stub )
         $s->write( "vy$cn" );
         else $s->writef( "(* $cl)" );
      $s->write( "( $t" );
      if ( $stub ) 
		 $this->writeThrowStub( $cn );
         else $s->write(" );\n");
   }

   /// interfész lekérő függvény
   protected function writeInterfArgs( $intf ) {
      $s = $this->stream;
      $un = strtoupper( $intf->name() );
      $s->writel();
      $s->writel( "#define VY%sARGS( ctx, name ) \\", $un );
      $s->indent(true);
      $s->writel( "VyArgs name = vyArgs( \"%s.%s\", vyVer(%s)); \\",
         $intf->pkg(), $intf->name(), substr($intf->ver(),1) );
      foreach ( $intf->types() as $t ) {
         $tn = $this->getType( $intf, $t->name(), false );
         $ch = substr( $tn, 0, 1 );
         if ( "&" == $ch )
            $re = sprintf("vyNative( ctx, \"%s\" )", substr($tn,1));
            else $re = "NULL";
         $s->writel( "vyArgsType( name, \"%s\", %s ); \\",
            $t->name(), $re );
      }
      foreach ( $this->ownTypes($intf) as $tn )
         $s->writel( "vyArgsFunc( name, \"set%s\"); \\", $tn );
      foreach ( $this->casts as $k=>$v ) {
		 $s->writel( "vyArgsFunc( name, \"%s\"); \\",
		    $this->getName( self::CASTL, $v ));
	  }
      foreach ( $intf->consts() as $c ) {
         $s->writel( "vyArgsFunc( name, \"%s\"); \\",
            $this->funcName( $c ));
      }
      foreach ( $intf->funcs() as $f ) {
         $s->writel( "vyArgsFunc( name, \"%s\"); \\",
            $f->name() );
      }
      $s->indent(false);
      $s->writel();
      $s->writel( "#define VYIMPORT%s( ctx, var ) \\", $un );
      $s->writel( "   VY%sARGS( ctx, var ## Args ); \\", $un );
      $s->writel( "   vyFree( vyGetImplem( ctx, var ## Args, & var )); \\" );
      $s->writel();
   }

   /// konstans esetén const kerül elé
   protected function funcName( $f, $intf = null ) {
      $name = $f->name();
      if ( $f->cons() && '&' == $name[0] )
         $name = "const".Tools::firstUpper( substr( $name, 1 ) );
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
	  $s->writel( "VY%sARGS( ctx, args );", strtoupper($name) );
	  foreach ($intf->types() as $t) {
         $tn = $this->getType( $intf, $t->name(), false );
         $ch = substr( $tn, 0, 1 );
         if ( "&" != $ch ) {
			$tnn = $tn;
			if ( "^" == $ch ) {
			   $tnn = substr( $tn, 1 );
			} else {
               $s->writel( "vyr%s = vyRepr( sizeof(struct %s), false, destroy%s);",
                  $tn, $tn, $tn );
            }
            $s->writel( 'vyArgsType( args, "%s", vyr%s );', $t->name(), $tnn );
         }
      }
      foreach ( $this->ownTypes($intf) as $tn ) {
         $s->writel( 'vyArgsImpl( args, "set%s", vy%sSet%s );', 
            $tn, $intf->name(), $tn );
      }
	  foreach ($intf->consts() as $c)
	     $this->writeBodyInitFunc( $c );
	  foreach ($intf->funcs() as $f)
	     $this->writeBodyInitFunc( $f );
	  foreach ($this->casts as $k=>$v)
	     $this->writeBodyInitCast( $intf, $k, $v );
	  $s->writel( "vyAddImplem( ctx, args );" );
	  $s->indent(false);
	  $s->writel("}\n");
   }	     
   
   /// init rész függvény kiírás
   protected function writeBodyInitFunc( $func ) {
      $fn = $this->funcName( $func );
	  $ffn = $this->funcName( $func, $func->owner() );
	  $this->stream->writel( 'vyArgsImpl( args, "%s", vy%s );', $fn, $ffn );
   }

   /// init rész cast kiírás
   protected function writeBodyInitCast( $intf, $t, $u ) {
	  $cn = $this->getName( self::CAST, $u, $intf );
	  $cl = $this->getName( self::CASTL, $u );
	  $this->stream->writel( 'vyArgsImpl( args, "%s", vy%s );', $cl, $cn );
   }

   /// interfész hasznos függvények
   protected function writeInterfUtil( $intf ) {
	  $s = $this->stream;
	  $name = $intf->name();
	  $s->writel( "void vyInit%s( VyContext );\n", $name );
   }	   

}
