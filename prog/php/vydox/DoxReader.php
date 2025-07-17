<?php

namespace vy;

/// dox olvasó
class DoxReader
   extends Configable
{

   /// állapotok
   const
      /// blokk kommentben van
      BLOCK = "block",
      /// a komment utáni rész közvetlenül
      REF = "ref",
      /// egyéb rész
      OUT = "out";

   /// nyitó- záró típusok
   const
      /// blokk komment nyitó
      CBLOCK = "cblock",
      /// blokk komment záró
      CEND = "cend",
      /// sor komment nyitó
      CLINE = "cline";

   /// olvasó típusok
   const
      C = "c",
      CPP = "cpp",
      ANY = "any",
      PHP = "php";

   /// típus kitalálása a fájlnévből
   static function guess( $fname ) {
      $e = strtolower( Tools::extension( $fname ) );
      switch ( $e ) {
         case ".dox": case ".txt": return self::ANY;
         case ".h": case ".c": return self::C;
         case ".hpp": case ".cpp": return self::CPP;
         case ".php": return self::PHP;
         default: return null;
      }
   }

   /// olvasó készítése típus alapján
   static function create( $t ) {
      switch ($t) {
         case self::C: return new DoxCReader();
         case self::CPP: return new DoxCppReader();
         case self::ANY: return new DoxReader();
         case self::PHP: return new DoxPhpReader();
         default: throw new EVy("Unknown dox input type: $t");
      }
   }

   /// az aktuális dox blokk
   protected $block;
   /// aktuális állapot
   protected $state;
   /// az olvasandó folyam
   protected $stream;
   /// a feldolgozás be van kapcsolva
   protected $on;
   /// eddig olvasott zárójelek
   protected $braces;

   /// az olvasó típusa
   function typ() { return self::ANY; }

   /// teljes fájl olvasása
   function readFile( $fname, $block ) {
      $this->init( $fname, $block );
      while ( false !== ($r = $this->readLine() ))
         $this->chew( $r );
   }

   /// alapállapot
   function init( $fname, $block ) {
      $this->state = self::OUT;
      $this->stream = new LStream( $fname );
      $this->block = $block;
      $this->braces = "";
      $this->on = true;
   }

   /// egy sor olvasása (záró \ jel felodlással)
   protected function readLine() {
      $ret = $this->stream->read();
      while ($ret && "\\" == substr( $ret, -1 ))
         $ret = substr( $ret, 0, -1 ).trim( $this->stream->read() );
      return $ret;
   }

   /// egy sor kezelése
   protected function chew( $r ) {
      switch ( $s = $this->state ) {
         case self::OUT:
         case self::REF:
            $i = $this->changer( $r, self::CLINE );
            $j = $this->changer( $r, self::CBLOCK );
            if ( null !== $j && (null === $i || $j < $i )) {
               $p = $this->chop( $r, $j );
               $this->setState( self::BLOCK, $p );
               if ( $r ) $this->chew( $r );
            } else if ( null !== $i ) {
               $p = $this->chop( $r, $i );
               $this->setState( self::REF, $p );
               $this->addDox( $r );
            } else if ( self::REF == $s )
               $this->addRef( $r, true );
            else
               $this->braces( $r );
         break;
         case self::BLOCK:
            $i = $this->changer( $r, self::CEND );
            if ( null !== $i ) {
               if ( $p = $this->chop( $r, $i ))
                  $this->addDox( $p );
               $this->setState( self::REF );
               if ( $r ) $this->chew( $r );
            } else if ( "\\" == substr( $r, -1 )) {
               $r = substr( $r, 0, -1 ).$this->stream->read();
               $this->chew( $r );
            } else
               $this->addDox( $r );
         break;
         default: throw new EVy("Unknown state: $s");
      }
   }

   /// a dox nyitó vagy záró helye
   protected function changer( $r, $kind ) {
      $this->sRex( $sr, $nr );
      switch ( $kind ) {
         case self::CBLOCK: $rx = '#^('."$sr|$nr".'*)/\*\*#'; break;
         case self::CLINE: $rx = '#^('."$sr|$nr".'*)///#'; break;
         case self::CEND: $rx = '#^(.*?)\*/#'; break;
         default: throw new EVy("Unknown kind: $kind");
      }
      if ( preg_match( $rx, $r, $m ))
         return strlen( $m[1] );
      return null;
   }

   /// string regexp
   protected function sRex( & $s, & $n ) {
      $s = '(".*(?<!\\\\)"|\'.*(?<!\\\\)\')';
      $n = '[^\'"]';
   }

   /// változató előtti rész levágása
   protected function chop( & $r, $i ) {
      $ret = substr( $r, 0, $i );
      $r = substr( $r, $i );
      if ( '/**' == substr( $r, 0, 3 )
         || '///' == substr( $r, 0, 3 ))
         $r = substr( $r, 3 );
      else if ( '*/' == substr( $r, 0, 2 ))
         $r = substr( $r, 2 );
      else
         throw new EVy("No changer found: $r\n");
      $r = trim( $r );
      return trim( $ret );
   }

   /// aktuális mélység
   protected function depth() {
      return strlen( $this->braces );
   }

   /// új rész készítése
   protected function addPart() {
      $this->block = $this->block->addPart( $this->depth() );
   }

   /// állapot változtatás
   protected function setState( $ns, $ref=null ) {
      $os = $this->state;
      if ( self::REF == $ns && self::OUT != $os && $this->block->refs() )
         $ns = self::OUT;
      if ( $ref && (self::BLOCK == $os || self::REF == $os))
         $this->addRef( $ref, false );
      if ( self::OUT == $os )
         $this->addPart();
      $this->state = $ns;
      if ( $ref && (self::OUT == $os))
         $this->addRef( $ref, false );
      if ( ! $this->container() && self::OUT == $ns )
         $this->up();
   }

   /// a blokknak lehetnek alblokkjai
   protected function container() {
      return in_array( $this->block->typ(), [DoxPart::CLS, DoxPart::RECORD, DoxPart::ENUM] );
   }

   /// dox sor hozzáadása
   protected function addDox( $r ) {
      $r = trim($r);
      $r = preg_replace_callback( '#\\\\var\s+([a-zA-Z0-9_]+)#',
         [$this,"doxVar"], $r );
      if ( preg_match('#^\\\\(\S+)(\s+(.*))?$#', $r, $m ))
         $this->addEsc( $m[1], Tools::g( $m, 3 ) );
         else $this->block->addRow( $r );
   }

   /// egy változó értékének olvasása
   protected function doxVar( $x ) {
      return Tools::g( $this->get( Dox::VARS ), $x[1] );
   }

   /// zárójelek kezelése
   protected function braces( $s ) {
      if ( ! $this->on ) return;
      $this->sRex( $sr, $nr );
      $s = preg_replace("#$sr#", '', $s);
      for ($i=0; $i<strlen($s); ++$i) {
         $c = $s[$i];
         $k = $this->braceKind( $c );
         if ( true === $k )
            $this->braces .= $c;
         else if ( false === $k ) {
            $this->braces = substr( $this->braces, -1 );
            if ( $this->depth() <= $this->block->depth() )
               $this->up();
         }
      }
   }

   /// blokk-zárójel típus
   protected function braceKind( $c ) {
      switch ($c) {
         case "{": return true;
         case "}": return false;
         default: return null;
      }
   }

   /// hivatkozott rész hozzáadása
   protected function addRef( $r, $quit ) {
      $r = trim($r);
      $cont = false;
      if ( $t = $this->refType( $r, $m ))
         $cont = $this->addRefTyp( $t, $m );
      $this->braces($r);
      if ( $quit || ! $cont )
         $this->setState( self::OUT );
   }

   /// referencia típusa
   protected function refType( $r, &$m ) {
      foreach ( $this->refRexs() as $k=>$v ) {
         if ( preg_match( $k, $r, $m ))
            return $v;
      }
      return null;
   }

   /// reguláris kifejezések a referencia olvasáshoz
   protected function refRexs() {
      return [];
   }

   /// referencia hozzáadás típussal
   protected function addRefTyp( $t, $m ) {
      $this->block->setTyp( $t );
      $this->block->addRef( $m[1] );
      if ( $n = Tools::g( $m, 2 ))
         $this->block->setName( $n );
      return false;
   }

   /// felsőbb szintre lépés, ha lehet
   protected function up() {
      if ( $o = $this->block->owner() )
         $this->block = $o;
   }

   /// escape-elt sor
   protected function addEsc( $esc, $r ) {
      switch( $esc ) {
         case DoxPart::NAME: return $this->block->setName( $r );
         case DoxPart::PARAM: return $this->addParam( $r );
         case DoxPart::REF: return $this->addRef( $r );
         case DoxPart::RETURN: return $this->addReturn( $r );
         case DoxPart::TOC: return $this->addToc();
         default: $this->block->addRow( "\\$esc $r" );
      }
   }

   /// paraméter hozzáadás
   protected function addParam( $r ) {
      if ( ! preg_match('#^\s*(\S+)\s+(.*)$#', $r, $m ))
         $m = [null,"?",$r];
      $p = $this->block->addPart();
      $p->setTyp( DoxPart::PARAM );
      $p->addRef( $m[1] );
      $p->addRow( $m[2] );
   }

   /// return hozzáadás
   protected function addReturn( $r ) {
      $p = $this->block->addPart();
      $p->setTyp( DoxPart::RETURN );
      $p->addRow( $r );
   }

   /// tartalomjegyzék hozzáadás
   protected function addToc() {
      $p = $this->block->addPart();
      $p->setTyp( DoxPart::TOC );
   }

}
