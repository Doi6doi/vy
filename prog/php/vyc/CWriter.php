<?php

namespace vy;

class CWriter {
	
   /// fázisok
   const
      ARGS = "args",
      BODYINCLUDE = "bodyInclude",
      HEADERINCLUDE = "headerInclude",
      HEADERHEAD = "headerHead",
      HEADERTAIL = "headerTail",
      IMPORT = "import",
      INIT = "init",
      INITDECL = "initDecl",
      STRUCT = "struct",
      STUB = "stub",
      TYPEDECL = "typeDecl",
      TYPEDEF = "typeDef";	
	
   /// kiíró
   protected $stream;
   /// Típus lekérdezés
   protected $map;
   /// kiírandó elemek
   protected $items;
   /// interfész
   protected $intf;
   /// reprezentációk
   protected $reprs;
   /// egyetlen saját típus
   protected $onlyOwn;

   function __construct() {
      $this->map = [];
      $this->items = [];
      $this->reprs = [];
   }
   
   /// objektum alapján c header kiírása
   function writeHeader( $obj, $fname ) {
      $this->stream = new OStream( $fname );
      try {
		 $this->createItems( $obj );
		 $this->writePhases( [ self::HEADERHEAD, self::HEADERINCLUDE, 
		    self::TYPEDECL, self::STRUCT, self::ARGS, self::IMPORT,
		    self::INITDECL, self::HEADERTAIL ] );
      } finally {
         $this->stream->close();
      }
   }

   /// objektum alapján c fájl kiírása
   function writeBody( $obj, $fname ) {
	  $this->stream = new OStream( $fname );
	  try {
		 $this->createItems( $obj );
		 $this->writePhases( [ self::BODYINCLUDE, 
		    self::TYPEDEF, self::STUB, self::INIT ] );
      } finally {
		  $this->stream->close();
	  }
   }

   function typeMap() { return $this->map; }

   /// típusmegfeleltetés beállítása
   function setTypeMap( array $map ) {
      $this->map = $map;
   }
   
   /// reprezentációk beállítása
   function setReprs( $r ) {
      if ( is_array( $r ))
         $this->reprs = $r;
      else if ( $r )
         $this->reprs = [$r];
      else $this->reprs = [];
   }
   
   /// itt definiált típus
   function own( $name ) {
      if ( $t = Tools::g( $this->map, $name ))
         return "*" == substr( $t, 0, 1 );
      return false;
   }
   
   /// egy típus reprezentációja
   function repr( $name ) {
      $t = "".Tools::g( $this->map, $name );
      if ( "*" == substr( $t, 0, 1 ))
         $t = substr( $t, 1 );
      if ( ! $t )
         $t = $name;   
      return $this->getRepr( $t );
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

   /// a modul neve
   protected function module() {
      return pathinfo( $this->filename(), PATHINFO_FILENAME );
   }

   /// a kimeneti fájl neve
   protected function filename() {
      return $this->stream->filename();
   }

   /// elemek elkészítése
   protected function createItems( $obj ) {
	  if ( $obj instanceof Interf )
	     $this->createInterfItems( $obj );
	  else
	     throw new EVy("Unknown object: ".get_class( $obj ));
   }

   /// interfész elemek készítése
   protected function createInterfItems( Interf $intf ) {
	  $this->items = [];
	  $this->onlyOwn = null;
	  $this->intf = $intf;
	  $map = $this->map;
	  foreach ( $intf->types() as $t )
		 $i = $this->addItem( CItem::TYPE, $t );
      foreach ( $intf->consts() as $c )
         $this->addItem( CItem::CONS, $c );
      foreach ( $intf->funcs() as $f )
         $this->addItem( CItem::FUNC, $f );
      if ( $this->onlyOwn )
         $this->onlyOwn->setOnly();
   }
        
   /// egy elem létrehozása
   protected function addItem( $kind, $obj ) {
	  $i = new CItem( $this, $kind, $obj );
	  $this->items [] = $i;
	  if ( CItem::TYPE == $kind && $i->own() )
         $this->onlyOwn = (null === $this->onlyOwn) ? $i : false;
	  return $i;
   }             

   /// fázisok kiírása
   protected function writePhases( array $phases ) {
	  foreach ( $phases as $p ) {
	     $this->writePhaseStart( $p );
	     foreach ( $this->items as $i )
	        $i->writePhase( $this->stream, $p );
	     $this->writePhaseFinish( $p );
	  }
   }
   
   /// egy fázis kiírása
   protected function writePhaseStart( $phase ) {
	  $s = $this->stream;
	  $intf = $this->intf;
	  switch ( $phase ) {
	     case self::TYPEDEF:
	     case self::STUB:
         case self::TYPEDECL:
	     break;
		 case self::HEADERHEAD:
            $hh = strtoupper( $this->module()."H" );
            $s->writel( "#ifndef $hh" );
            $s->writel( "#define $hh\n" );
         break;
         case self::HEADERTAIL:
            $hh = strtoupper( $this->module()."H" );
            $s->writel( "#endif // $hh");
         break;   
         case self::HEADERINCLUDE:
            if ( $this->hasOwnPublic() )
               $s->writel("#include <vy_implem.h>\n");
               else $s->writel( "#include <vy.h>\n" );
         break;
         case self::STRUCT:
            if ( 0 < $this->itemCount( CItem::FUNC ) 
               + $this->itemCount( CItem::CONS )
            ) {
               $s->writel( "typedef struct %sFun {", $intf->name() );
               $s->indent(true);
            }
         break;
         case self::ARGS:
            $un = strtoupper( $intf->name() );
            $s->writel( "#define VY%sARGS( ctx, name ) \\", $un );
            $s->indent(true);
            $s->writel( "VyArgs name = vyArgs( \"%s.%s\", vyVer(%s)); \\",
               $intf->pkg(), $intf->name(), substr($intf->ver(),1) );
         break;
         case self::IMPORT:
            $un = strtoupper( $intf->name() );
            $s->writel( "#define VYIMPORT%s( ctx, var ) \\", $un );
            $s->writel( "   VY%sARGS( ctx, var ## Args ); \\", $un );
            $s->writel( "   vyFree( vyGetImplem( ctx, var ## Args, & var ));\n" );
         break;
         case self::INITDECL:
            $s->writel( "void vyInit%s( VyContext );\n", $intf->name() );
         break;
         case self::BODYINCLUDE:
            $s->writel( '#include <vy_implem.h>');
	        $s->writel( "#include \"%s.h\"\n", $this->module() );
	     break;
	     case self::INIT:
	        $n = $intf->name();
            $s->writel( "void vyInit%s( VyContext ctx ) {", $n );
	        $s->indent(true);
	        $s->writel( "VY%sARGS( ctx, args );", strtoupper($n) );
	     break;
	     default:
	        throw new EVy("Unknown phase: $phase");
	  }
   }

   /// egy fázis kiírása
   protected function writePhaseFinish( $phase ) {
	  $s = $this->stream;
	  $intf = $this->intf;
	  switch ( $phase ) {
		 case self::STRUCT:
            if ( 0 < $this->itemCount( CItem::FUNC ) 
               + $this->itemCount( CItem::CONS )
            ) {
  	           $s->indent( false );
               $s->writel( "} %sFun;\n", $intf->name() );
            }
         break;
         case self::ARGS:
            $s->indent(false);
            $s->writel();
         break;
         case self::INIT:
		    $s->writel( "vyAddImplem( ctx, args );" );
		    $s->indent( false );
            $s->writel( "}\n" );
         break;
      }
   }

   /// van-e saját típus
   protected function hasOwnPublic() {
      foreach ($this->map as $k=>$v) {
         if ( "*" == substr( $v, 0, 1 ))
            if ( $this->repr( $k )->public() )
               return true;
      }
      return false;
   }

   /// bizonyos típusú elemek száma
   protected function itemCount( $kind ) {
	  $ret = 0;
	  foreach ( $this->items as $i ) {
	     if ( $kind == $i->kind() )
	        ++$ret;
	  }
	  return $ret;
   }
      
		 
}
