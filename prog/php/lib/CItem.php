<?php

namespace vy;

/// c kiíráshoz elem
class CItem {
	
	const
	   TYPE = "type",
	   CAST = "cast",
	   CONS = "cons",
	   FUNC = "func";
	
	protected $kind;
	protected $obj;
	protected $map;
	protected $extra;
	protected $tKind;
	protected $only;
	
	function __construct( $kind, $obj, $extra = null ) {
	   $this->kind = $kind;
	   $this->obj = $obj;
	   $this->map = $this->extra = $extra;
	   switch ( $kind ) {
	      case self::TYPE: $this->initType(); break;
	      case self::CONS: $this->initCons(); break;
	   }
	}

   /// saját típus
   function own() { 
	  return self::TYPE == $this->kind && ! $this->tKind; 
   }
	
   /// egyetlen elem beállítás
   function setOnly() {
	  $this->only = true;
   }	
	
   /// fázis kiírása
   function writePhase( OStream $s, $phase ) {
	  switch ( $phase ) {
	     case CWriter::HEADERHEAD:
		 case CWriter::HEADERINCLUDE:
		    return;
		 case CWriter::TYPEDECL:
		    return $this->writeTypeDecl( $s );
		 case CWriter::TYPEDEF:
		    return $this->writeTypeDef( $s );
		 case CWriter::STRUCT:
		    return $this->writeStruct( $s );
		 case CWriter::ARGS:
		    return $this->writeArgs( $s );
		 case CWriter::STUB:
		    return $this->writeStub( $s );
		 case CWriter::INIT:
		    return $this->writeInit( $s );
		 case CWriter::BODYINCLUDE:
		 case CWriter::HEADERTAIL:
		 case CWriter::IMPORT:
		 case CWriter::INITDECL;
		    return;
		 default: throw new EVy("Unknown phase: $phase");
	  }
   }	

   /// típus inicializálás
   protected function initType() {
	  $this->extra = $this->getType( $this->obj->name() );
	  $this->tKind = $this->trim( $this->extra );
   }

   /// konstans inicializálás
   protected function initCons() {
	  $this->extra = $this->getType( $this->obj->name() );
	  $this->tKind = $this->trim( $this->extra );
   }

   /// típus beállítása
   protected function getType( $typ ) {
	  if ( $ret = Tools::g( $this->map, $typ ) )
	     return $ret;
	  return $typ;
   }

   /// jel trim-elés
   protected function trim( & $nam ) {
	  $ch = substr( $nam, 0, 1 );
	  if ( in_array( $ch, ["&","^"] )) {
	     $nam = substr($nam,1);
	     return $ch;
	  } else
	     return null;
   }
	  
   /// típusdeklaráció kiírása
   protected function writeTypeDecl( $s ) {
	  if ( ! in_array( $this->kind, [self::TYPE, self::CAST] )) return;
	  if ( "&" != $this->tKind ) {
		 $t = $this->extra;
         $s->writel( "typedef struct %s * %s;\n", $t, $t );
      }
   }
   
   /// típusdefiníció kiírása
   protected function writeTypeDef( $s ) {
	  if ( $this->own() ) {
         $s->writel( "struct %s {", $this->extra );
         $s->indent(true);
         $s->writel( "VyRepr repr;" );
         $s->indent(false);
         $s->writel("};\n");
         $s->writel( "VyRepr vyr%s;\n", $this->extra );
      } else if ( "^" == $this->tKind ) {
         $s->writel( "extern VyRepr vyr%s;\n", $this->extra );
      }
   }
   
   /// kiírás struktúrában
   protected function writeStruct( $s ) {
	  switch ( $this->kind ) {
		 case self::TYPE: 
		    if ( $this->own() )
		       $this->writeSetter( $s, false );
		 return;
		 case self::CAST:
		    $e = $this->extra;
            $s->writel( "%s (* %s)( %s );", 
               $e, $this->shortName(), $this->obj );
         break;
         case self::CONS: return $this->writeConst( $s, false );
         case self::FUNC: return $this->writeFunc( $s, false );
         default:
            throw $this->unKind();
      }
   }
   
   /// függvény rövid neve
   function shortName() {
	  switch ( $this->kind ) {
		 case self::CAST:
		    return "cast".( $this->only ? "" : $this->extra );
		 case self::CONS:
		    if ( $this->tKind )
		       return "const".Tools::firstUpper( $this->extra );
		       else return $this->extra;
		 case self::FUNC:
		    return $this->obj->name();
		 case self::TYPE:
		    return "set".( $this->only ? "" : $this->extra );
		 default:
		    throw $this->unKind();
      }
   }
   
   /// függvény hosszú neve
   function longName() {
      if ( self::CAST == $this->kind )
         $ow = $this->obj;
         else $ow = $this->obj->owner()->name();
	  return sprintf( "vy%s%s", $ow, Tools::firstUpper( $this->shortName() ));
   }
   
   /// nem ismert fajta
   protected function unKind() {
	  return new EVy("Unknown kind:".$this->kind);
   }
   
   /// konstans kiírása
   protected function writeConst( $s, $stub ) {
	  $f = $this->obj;
      if ( $stub )
         $s->write( "static " );
         else $s->writeIndent();
      $t = $this->getType( $f->sign()->result() );
      $tk = $this->trim( $t );
      $s->writef( "$t " );
      if ( $stub )
	     $s->writef( "%s( ", $this->longName() );
	     else $s->writef("(* %s)( ", $this->shortName() );
      if ( "&" == $this->tKind ) {
         switch ( $this->extra ) {
            case "ascii": case "utf": case "hex":
               $s->write("VyCStr, VySize");
            break;
            case "dec":
               $s->write("VyDec");
            break;
            default: throw new EVy("Unknown special constant:".$this->extra);
         }
      }
      if ( $stub )
		 $this->writeThrowStub($s);
         else $s->write(" );\n");
   }

   /// konstans kiírása
   protected function writeSetter( $s, $stub ) {
	  if ( ! $this->own() ) return;
      if ( $stub )
         $s->write( "static " );
         else $s->writeIndent();
      $s->writef( "void " );
      if ( $stub )
	     $s->writef( "%s( ", $this->longName() );
	     else $s->writef("(* %s)( ", $this->shortName() );
      $s->writef("%s *, %s", $this->extra, $this->extra );
      if ( $stub )
		 $this->writeThrowStub($s);
         else $s->write(" );\n");
   }

   /// interfész struktúra függvény kiírása
   protected function writeFunc( $s, $stub ) {
	  $f = $this->obj;
      if ( $stub )
         $s->write( "static " );
         else $s->writeIndent();
      if ( $t = $this->getType( $f->sign()->result() ) ) {
		 $tk = $this->trim( $t );
         $s->write( "$t " );
      } else
		 $s->write( "void " );
      if ( $stub )
	     $s->writef( "%s( ", $this->longName() );
	     else $s->writef("(* %s)( ", $this->shortName() );
      $first = true;
      foreach ( $f->sign()->args() as $a ) {
         if ( $first )
            $first = false;
            else $s->write(", ");
         $t = $this->getType( $a->type() );
         $tk = $this->trim( $t );
         $s->write( "$t" );
         if ( $n = $a->name() )
            $s->write(" $n");
      }
      if ( $stub )
		 $this->writeThrowStub($s);
         else $s->write(" );\n");
   }
   
   /// argumentum makró kiírása
   protected function writeArgs( $s ) {
	  switch ( $this->kind ) {
		 case self::TYPE: 
		    if ( "&" == $this->tKind )
               $re = sprintf("vyNative( ctx, \"%s\" )", $this->extra );
               else $re = "NULL";
            $s->writel( "vyArgsType( name, \"%s\", %s ); \\",
               $this->obj->name(), $re );
            if ( $this->own() )
               $s->writel( "vyArgsFunc( name, \"%s\"); \\", $this->shortName() );
         break;
         case self::CAST:
         case self::CONS:
         case self::FUNC:
            $s->writel( "vyArgsFunc( name, \"%s\"); \\", $this->shortName() );
         break;
         default:
            throw $this->unKind();
      }
   }
   
   /// stub-ok kiírása
   protected function writeStub( $s ) {
	  switch ( $this->kind ) {
		 case self::TYPE: 
		    if ( $this->own() ) {
		       $s->writef( "void vyDestroy%s( VyPtr", $this->extra );
		       $this->writeThrowStub( $s, "vyDestroy".$this->extra );
		       $this->writeSetter( $s, true );
		    }
		 break;
         case self::CAST:
            $s->writef( "%s %s( %s", $this->extra, $this->longName(),
               $this->obj );
            $this->writeThrowStub($s);
         break;
         case self::CONS: return $this->writeConst( $s, true );
         case self::FUNC: return $this->writeFunc( $s, true );
         default:
            throw $this->unKind();
      }
   }

   /// init rész kiírása
   protected function writeInit( $s ) {
	  switch ( $this->kind ) {
		 case self::TYPE:
 		    $tn = $this->extra;
		    if ( $this->own() ) {
               $s->writel( "vyr%s = vyRepr( sizeof(struct %s), false, vyDestroy%s);",
                  $tn, $tn, $tn );
            }
            if ( "&" != $this->tKind ) {
               $s->writel( 'vyArgsType( args, "%s", vyr%s );', 
                  $this->obj->name(), $tn );
            }
            if ( $this->own() ) {
               $s->writel( 'vyArgsImpl( args, "%s", %s );', 
                  $this->shortName(), $this->longName() );
            }
         break;
         case self::CAST:
         case self::CONS:
         case self::FUNC:
            $s->writel( 'vyArgsImpl( args, "%s", %s );', 
               $this->shortName(), $this->longName() );
         break;
         default: throw $this->unKind();
      }
   }


   /// stub kivétel
   protected function writeThrowStub( $s, $name=null ) {
	  $s->writel(" ) {");
	  $s->indent(true);
      $s->writel("vyThrow(\"stub %s\");", $name ? $name : $this->longName() );
	  $s->indent(false);
	  $s->writel("}\n");
   }

   
}
