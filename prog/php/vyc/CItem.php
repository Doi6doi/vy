<?php

namespace vy;

/// c kiíráshoz elem
class CItem {
	
	const
	   TYPE = "type",
	   CAST = "cast",
	   CONS = "cons",
	   FUNC = "func";
	
   protected $writer;
	protected $kind;
	protected $obj;
	protected $only;
	
	function __construct( CWriter $writer, $kind, $obj ) {
      $this->writer = $writer;
	   $this->kind = $kind;
	   $this->obj = $obj;
	}

   /// saját típus
   function own() { 
      return in_array( $this->reprKind(), 
         [Repr::REFCOUNT, Repr::MANAGED] );
   }

   function extra() { return $this->extra; }

   function reprKind() { return $this->repr()->kind(); }
   
   /// a reprezentáció
   function repr( $name=null ) {
      if ( ! $name )
         $name = $this->obj->name();
      if ( ! $ret = $this->writer->repr( $name ))
         throw new EVy("Unknown repr: $name");
      return $ret;
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
		    return $this->writeBodyInclude( $s );
		 case CWriter::HEADERTAIL:
		 case CWriter::IMPORT:
		 case CWriter::INITDECL;
		    return;
		 default: throw new EVy("Unknown phase: $phase");
	  }
   }	

   /// típusdeklaráció kiírása
   protected function writeTypeDecl( $s ) {
	  if ( self::TYPE != $this->kind ) return;
	  switch ( $this->reprKind() ) {
		 case Repr::NATIVE: return;
		 case Repr::INHERIT: 
		    $t = $this->repr()->old();
          $s->writel( "typedef struct %s * %s;\n", $t, $t );
		 break;
		 default: 
      }
      $t = $this->repr()->name();
      $s->writel( "typedef struct %s * %s;\n", $t, $t );
   }
   
   /// típusdefiníció kiírása
   protected function writeTypeDef( $s ) {
	  if ( self::TYPE != $this->kind ) return; 
	  $tk = $this->tKind;
	  $e = $this->extra;
	  switch ( $tk ) {
		 case null: case "#": case ":":
		    $tt = $this->extra;
		    if ( ":" == $tk )
		       $tt = $this->obj->name();
            $s->writel( "struct %s {", $tt );
            $s->indent(true);
            switch ( $tk ) {
			   case "#": $l = "VyRefCount ref"; break;
			   case ":": $l = sprintf( "struct %s %s", $this->extra,
			      Tools::firstLower( $this->extra )); break;
			   default: $l = "VyRepr repr";
			}
			$s->writel( "$l;" );
            $s->indent(false);
            $s->writel("};\n");
         break;
      }
      switch ( $tk ) {
		 case ":": $s->writel("extern VyRepr vyr%s;\n", $this->extra ); break;
		 case "^": $s->write("extern "); break;
      }
      if ( "&" != $tk )
         $s->writel( "VyRepr vyr%s;\n", $tt );
   }
   
   /// kiírás struktúrában
   protected function writeStruct( $s ) {
	  switch ( $this->kind ) {
		  case self::TYPE: 
		     if ( $this->own() )
		        $this->writeSetter( $s, false );
		  return;
        case self::CAST:
		     $e = $this->repr()->old();
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
		    return "cast".( $this->only ? "" : $this->repr()->str() );
		 case self::CONS:
          return "const".Tools::firstUpper( $this->obj->name() );
		 case self::FUNC:
		    return $this->obj->name();
		 case self::TYPE:
		    return "set".( $this->only ? "" : $this->repr()->str() );
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
      $t = $this->repr( $f->sign()->result() )->str();
      $s->writef( "$t " );
      if ( $stub )
	     $s->writef( "%s( ", $this->longName() );
	     else $s->writef("(* %s)( ", $this->shortName() );
      if ( $this->obj->special() ) {
         switch ( $n = $this->obj->name() ) {
            case "ascii": case "utf": case "hex":
               $s->write("VyCStr, VySize");
            break;
            case "dec":
               $s->write("VyDec");
            break;
            default: throw new EVy("Unknown special constant: $n" );
         }
      }
      if ( $stub )
		 $this->writeThrowStub($s);
         else $s->write(" );\n");
   }

   /// setter kiírása
   protected function writeSetter( $s, $stub ) {
      if ( $stub )
         $s->write( "static " );
         else $s->writeIndent();
      $s->writef( "void " );
      if ( $stub )
	      $s->writef( "%s( ", $this->longName() );
	      else $s->writef("(* %s)( ", $this->shortName() );
	   $t = $this->repr()->str();
      $s->writef("%s * dest, %s val", $t, $t );
      if ( $stub ) {
		   $s->write( ") {\n" );
		   $s->indent(true);
		   $s->writel("vySetter( (VyAny *)dest, (VyAny)val );" );
		   $s->indent(false);
		   $s->writel("}\n");
      } else 
         $s->write(" );\n");
   }

   /// interfész struktúra függvény kiírása
   protected function writeFunc( $s, $stub ) {
	  $f = $this->obj;
      if ( $stub )
         $s->write( "static " );
         else $s->writeIndent();
      if ( $r = $f->sign()->result() )
         $s->write( $this->repr( $r )->str() );
         else $s->write( "void " );
      if ( $stub )
	     $s->writef( "%s( ", $this->longName() );
	     else $s->writef("(* %s)( ", $this->shortName() );
      $first = true;
      foreach ( $f->sign()->args() as $a ) {
         if ( $first )
            $first = false;
            else $s->write(", ");
         $t = $this->repr( $a->type() )->str();
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
          if ( Repr::NATIVE == $this->reprKind() )
             $re = sprintf("vyNative( ctx, \"%s\" )", $this->repr()->str() );
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

   /// body include rész kiírása
   protected function writeBodyInclude( $s ) {
	  if ( self::TYPE == $this->kind
	     && in_array( $this->reprKind(), [Repr::INHERIT] ))
	  {
	     $s->writel("#include \"vy_%s.h\"\n", 
	        Tools::firstLower( $this->extra ));
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
