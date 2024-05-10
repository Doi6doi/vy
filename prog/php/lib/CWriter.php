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
   /// Cast-ok
   protected $casts;
   /// kiírandó elemek
   protected $items;
   /// interfész
   protected $intf;
   /// egyetlen cast
   protected $onlyCast;
   /// egyetlen saját típus
   protected $onlyOwn;

   function __construct() {
      $this->map = [];
      $this->casts = [];
      $this->items = [];
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
	  $this->onlyCast = null;
	  $this->onlyOwn = null;
	  $this->intf = $intf;
	  $map = $this->map;
	  foreach ( $intf->types() as $t )
		 $this->addItem( CItem::TYPE, $t, $map );
      foreach ( $this->casts as $k=>$v )
         $this->addItem( CItem::CAST, $k, $v );
      foreach ( $intf->consts() as $c )
         $this->addItem( CItem::CONS, $c, $map );
      foreach ( $intf->funcs() as $f )
         $this->addItem( CItem::FUNC, $f, $map );
      if ( $this->onlyCast )
         $this->onlyCast->setOnly();
      if ( $this->onlyOwn )
         $this->onlyOwn->setOnly();
   }
        
   /// egy elem létrehozása
   protected function addItem( $kind, $obj, $extra=null ) {
	  $i = new CItem( $kind, $obj, $extra );
	  $this->items [] = $i;
	  if ( CItem::TYPE == $kind && $i->own() )
	     $this->onlyOwn = (null === $this->onlyOwn) ? $i : false;
	  else if ( CItem::CAST == $kind ) 
	     $this->onlyCast = (null === $this->onlyCast) ? $i : false;   
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
            $s->writel( "#include <vy.h>\n" );
         break;
         case self::STRUCT:
            $s->writel( "typedef struct %sFun {", $intf->name() );
            $s->indent(true);
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
		    $s->indent( false );
            $s->writel( "} %sFun;\n", $intf->name() );
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
		 
}
