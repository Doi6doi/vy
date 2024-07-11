<?php

namespace vy;

/// c kiíráshoz elem
class CItem {
	
	const
	   TYPE = "type",
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
      return $this->writer->own( $this->obj->name() );
   }
 
   function kind() { return $this->kind; }

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
		    return $this->writeTypeDef( $s, true );
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
      $r = $this->repr();
      if ( Repr::NATIVE == $r->kind() ) return;
	   if ( Repr::INHERIT == $r->kind() ) {
	      $t = $this->repr()->old();
         $s->writel( "typedef struct %s * %s;\n", $t, $t );
      }
      $t = $this->repr()->name();
      $s->writel( "typedef struct %s * %s;\n", $t, $t );
      $this->writeTypeDef( $s, false );
   }
   
   /// típusdefiníció kiírása
   protected function writeTypeDef( $s, $impl ) {
	  if ( self::TYPE != $this->kind ) return; 
     $r = $this->repr();
     if ( Repr::NATIVE == $r->kind() ) return;
     if ( $this->own() ) {
        if ( $r->public() != $impl ) {
           $s->writel( "struct %s {", $r->str() );
           $s->indent( true );
           switch ( $r->kind() ) {
              case Repr::CUSTOM: $s->writel("VyRepr repr;"); break;
              case Repr::INHERIT:
                 $s->writel("struct %s %s;", $r->old(), 
                    Tools::firstLower( $r->old() ));
              break;
              case Repr::REFCOUNT: $s->writel("struct VyRefCount ref;"); break;
              default: throw $r->unKind();
           }
           if ( $fs = $r->fields() ) {
              foreach ( $fs as $k=>$v ) {
                 $fr = $this->repr( $v );
                 $s->writel("%s %s;", $fr->str(), $k );
              }
           }
           $s->indent( false );
           $s->writel("};\n");
           $s->writel("void vy%sInit( %s );\n", $r->str(), $r->str() );
        }
     }
     if ( $impl ) {
        $s->writel("%sVyRepr vyr%s;\n", $this->own() ? "": "extern ", $r->str() ); 
        if ( Repr::INHERIT == $r->kind() )
           $s->writel("extern VyRepr vyr%s;\n", $r->old() );
     }
   }
   
   /// kiírás struktúrában
   protected function writeStruct( $s ) {
	  switch ( $this->kind ) {
        case self::TYPE: break;
        case self::CONS: return $this->writeConst( $s, false );
        case self::FUNC: return $this->writeFunc( $s, false );
        default:
           throw $this->unKind();
      }
   }
   
   /// függvény rövid neve
   function shortName() {
	  switch ( $this->kind ) {
		 case self::CONS:
          return "const".Tools::firstUpper( $this->obj->name() );
		 case self::FUNC:
		    return $this->obj->name();
		 default:
		    throw $this->unKind();
      }
   }
   
   /// függvény hosszú neve
   function longName() {
     $ow = $this->obj->owner()->name();
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

   /// interfész struktúra függvény kiírása
   protected function writeFunc( $s, $stub ) {
	  $f = $this->obj;
      if ( $stub )
         $s->write( "static " );
         else $s->writeIndent();
      if ( $r = $f->sign()->result() )
         $s->write( $this->repr( $r )->str()." " );
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
       break;
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
            $r = $this->repr();
		      if ( $this->own() ) {
               $sn = "vyDestroy".$r->str();
               $s->writef( "void $sn( VyPtr" );
	 	         $this->writeThrowStub( $s, $sn );
		      }
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
	        Tools::firstLower( $this->repr()->old() ));
	  }
   } 

   /// init rész kiírása
   protected function writeInit( $s ) {
      switch ( $this->kind ) {
		   case self::TYPE:
            $r = $this->repr();
            $rs = $r->str();
		      if ( $this->own() ) {
               $s->writel( "vyr%s = vyRepr( \"%s\", sizeof(struct %s), %s, vyDestroy%s);",
                  $rs, $rs, $rs, $this->setter(), $rs );
            }
            if ( Repr::NATIVE == $r->kind() )
               $ars = sprintf( 'vyNative(ctx,"%s")', $r->str() );
               else $ars = sprintf( "vyr%s", $rs );
            $s->writel( 'vyArgsType( args, "%s", %s );', 
               $this->obj->name(), $ars );
         break;
         case self::CONS:
         case self::FUNC:
            $s->writel( 'vyArgsImpl( args, "%s", (VyPtr)%s );', 
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

   /// beállító függvény
   protected function setter($typ=null) {
      $r = $this->repr($typ);
      switch ( $r->kind() ) {
         case Repr::CUSTOM: return "vySetCustom";
         case Repr::REFCOUNT: return "vySetRef";
         case Repr::MANAGED: return "vySetManaged";   
         case Repr::INHERIT: return $this->setter( $r->old() );
         default: throw $r->unKind();
      }
   }
   
}
