<?php

namespace vy;

class Make 
   implements ExprCtx
{

   const
      MAKEVY = "Make.vy";

   const
      ERROR = 1,
      WARNING = 2,
      INFO = 3;

   const
      FUNCTION = "function",
      IMPORT = "import",
      INIT = "init",
      MAKE = "make",
      TARGET = "target";

   static function load( $fname ) {
      $ret = new Make();
      $s = new ExprStream( $fname );
      $ret->read( $s );
      $s->close();
      return $ret;
   }

   /// inicializáló
   protected $init;
   /// célok
   protected $targets;
   /// egyéb nevek
   protected $names;
   /// futtatási környezet
   protected $runCtx;
   /// alap függvények
   protected $core;
   /// naplózási szint
   protected $level;
   
   function __construct() {
	  $this->targets = [];
	  $this->names = [];
	  $this->core = new MakeCore( $this );
	  foreach ( $this->core->names() as $f )
	     $this->add( $this->names, $f->name(), $f );
	  $this->level = self::INFO;
   }

   function blockKind() { return Block::BODY; }

   function init() { return $this->init; }

   function defType() { return null; }

   /// futtatás célokkal
   function run( $target ) {
	  $target = $this->refineTarget( $target );
	  $this->runCtx = new RunCtx();
     $this->runInit();
	  foreach ( $target as $t )
	     $this->runTarget( $t );
   }

   /// inicializáló futtatása
   function runInit( ) {
      if ( ! $this->init ) return;
      $this->init->call( $this->runCtx, [] );
   }

   /// egy cél futtatása
   function runTarget( $target ) {
	  if ( ! $t = Tools::g( $this->targets, $target ))
	     throw new EVy("Unknown target: $target");
	  $t->call( $this->runCtx, [] );
   }

   function checkType( $type ) {
      throw new EVy("Unknown type: $type");
   }

   function readType( ExprStream $s ) {
      throw new EVy("Cannot read type");
   }

   function canCall( $x ) { return true; }

   function setLevel( $v ) {
	  $this->level = $v;
   }

   function log( $lvl, $msg ) {
	  if ( $this->level < $lvl )
	     return;
	  print( "$msg\n" );
   }

   function resolve( $token, $kind ) {
      switch ( $kind ) {
		   case ExprCtx::NAME:
	         if ( $n = Tools::g( $this->names, $token ))
	            return $n;
	         $ret = new MakeVar($token);
	         $this->names[$token] = $ret;
	         return $ret;
		}
   	throw new EVy("Cannot resolve $kind: $token " );
   }

   /// aktuális stack kiírása
   function dumpStack() {
      print( $this->runCtx->dump()."\n\n" );
   }

   /// make file beolvasása
   protected function read( Stream $s ) {
      try {
         $s->readWS();
	      $s->readToken( self::MAKE );
	      $s->readWS();
	      $s->readToken("{");
	      while ( $this->readPart($s) )
	         ;
	      $s->readToken("}");
      } catch ( \Exception $e ) {
         throw new EVy( $s->position().": ".$e->getMessage(), 0, $e );
      }
   }
	
   /// egy rész olvasása
   protected function readPart( $s ) {
      $s->readWS();
      switch ( $n = $s->next() ) {
         case self::INIT: return $this->readInit($s);
		   case self::IMPORT: $meth = "readImport"; break;
         case self::TARGET: $meth = "readTarget"; break;
         case self::FUNCTION: $meth = "readFunction"; break;
         case "}": return false;
         default: throw new EVy("Unknown part: $n");
      }
      $this->readPartBlock( $s, $meth );
      return true;
   }
	
   /// részblokk olvasása
   protected function readPartBlock( $s, $meth ) {
      $s->read();
      $s->readWS();
      $clb = [$this,$meth];
      if ( ! $s->readIf("{")) {
         call_user_func( $clb, $s );
      } else {
         while (true) {
            $s->readWS();
            if ( $s->readIf("}"))
               return;
            call_user_func( $clb, $s );
         }
      }
   }
	
   /// hozzáadás egy listához
   protected function add( & $arr, $name, $obj ) {
      if ( array_key_exists( $name, $arr ) )
         throw new EVy("Duplicate name: $name");
      $arr[$name] = $obj;
   }
	
   /// target rész beolvasása
   protected function readTarget( $s ) {
	  $t = new MakeTarget($this);
	  $t->read( $s );
	  $this->add( $this->targets, $t->name(), $t );
     $this->addFunc( $t );
   }

   /// init rész beolvasása
   protected function readInit( $s ) {
      if ( $this->init )
         throw new EVy("Multiple init blocks");
      $this->init = new MakeTarget( $this );
      $this->init->read( $s );
      return true;
   }

   /// import rész beolvasása
   protected function readImport( $s ) {
	  $s->readWS();
	  $name = $s->readIdent();
	  $i = MakeImport::load( $this, $name );
	  $this->add( $this->names, $name, $i );
	  $s->readWS();
	  $s->readToken(";");
	  $i->start();
   }

   /// függvény rész beolvasása
   protected function readFunction( ExprStream $s ) {
	  $f = new MakeFunc( $this );
	  $f->read( $s );
     $this->addFunc( $f );
   }

   /// függvény rögzítése a nevekhez
   protected function addFunc( $f ) {
	  $name = $f->name();
	  if ( $old = Tools::g( $this->names, $name )) {
	     if ( ! ($old instanceof MakeVar) )
	        throw new EVy("Cannot redefine function: ".$name );
	     $old->setValue( $f );
	  } else {
         $this->add( $this->names, $f->name(), $f );
      }
   }
	  
   /// a cél egy nem üres tömb legyen
   protected function refineTarget( $target ) {
	  if ( ! $target ) {
		 if ( ! $this->targets )
		    throw new EVy("Make file does not have targets");
		 foreach ( $this->targets as $k=>$v)
		    return [$k];
      } else if ( ! is_array( $target ))
         return [$target];
      else
         return $target;
   }

	
}
