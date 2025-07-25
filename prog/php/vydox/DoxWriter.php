<?php

namespace vy;

/// dox író
abstract class DoxWriter
   extends Configable
{

   /// író típusok
   const
      ANSI = "ansi",
      HTML = "html",
      MAN = "man",
      MD = "md",
      TXT = "txt";

   /// kiírandó részek
   const
      BR = "br",
      CODE = "code",
      EM = "em",
      HEAD = "head",
      LINK = "link",
      LST = "lst",
      REF = "ref",
      PARTS = "parts",
      REFS = "refs",
      ROWS = "rows",
      STRONG = "strong";

   /// elválasztó fajták
   const
      SPART = "spart",
      SREF = "sref",
      SREFS = "srefs",
      SROW = "srow",
      SROWS = "srows";

   /// típus kitalálása a fájlnévből
   static function guess( $fname ) {
	  if ( ! $fname ) return null;
      $e = strtolower( Tools::extension( $fname ) );
      switch ( $e ) {
         case ".htm": case ".html": return self::HTML;
         case ".md": return self::MD;
         case ".txt": return self::TXT;
         default:
            if ( preg_match('#^\\.\d$#', $e ))
               return self::MAN;
            return null;
      }
   }

   /// író készítése típus alapján
   static function create( $t ) {
      switch ($t) {
         case self::MD: return new DoxMdWriter();
         case self::HTML: return new DoxHtmlWriter();
         case self::TXT: return new DoxTxtWriter();
         case self::ANSI: return new DoxAnsiWriter();
         default: throw new EVy("Unknown dox output type: $t");
      }
   }

   /// az író típusa
   abstract function typ();
   /// az épp írandó blokk
   protected $block;

   /// szöveg formázása
   function format( $r ) {
      $rxps = [
         '#^\s*$#'=>self::BR,
         '#\*\*([^*]+)\*\*#'=>self::STRONG,
         '#\*(\S[^*]*)\*#'=>self::EM,
         '#`([^`]+)`#'=>self::CODE,
         '#\[([^\]]+)\](?:\(([^ ()]+)\))?#'=>self::LINK,
         ':^(#+)\s+(.*):'=>self::HEAD,
         '#^\s*\*\s+(.*)#'=>self::LST
      ];
      foreach ( $rxps as $k=>$v )
         $r = preg_replace_callback( $k, function($m) use ($v) {
            return $this->formatPart( $v, $m );
         }, $r );
      return $r;
   }

   /// blokk kiírása stringbe
   function write( $block ) {
      $this->block = $block;
      $f = $this->writeRefs( $block );
      $r = $this->writeRows( $block );
      $p = $this->writeParts( $block );
      return $this->writePart( $f, $r, $p );
   }

   /// blokk kiírása részekből
   protected function writePart( $refs, $rows, $parts ) {
      return $this->join( $refs, self::SREFS,
         $this->join( $rows, self::SROWS, $parts ));
   }

   /// részek összekötése elválasztóval
   protected function join( $a, $sep, $b ) {
      if ( $a && $b )
         return $a.$this->sep($sep).$b;
         else return $a.$b;
   }

   /// elválasztó
   protected function sep($kind) {
      switch ($kind) {
         case self::SREF: case self::SROW:
            return "\n";
         default: return "\n\n";
      }
   }

   /// referencia rész kiírása
   protected function writeRefs( $b ) {
      if ( ! $fs = $b->refs() ) return null;
      $ret = implode( $this->sep( self::SREF ), $fs );
      return $this->formatPart( self::REFS, [$ret] );
   }

   /// id építése
   protected function getId( $b ) {
      if ( ! $n = $b->name() ) return false;
      if ( ! $o = $b->owner() ) return false;
      if ( ! $oo = $o->owner() ) return $n;
      if ( $on = $this->getId($o) )
         return $on.".".$n;
      return false;
   }

   /// szöveges rész kiírása
   protected function writeRows( $b ) {
      if ( ! $rs = $b->rows() ) return null;
      $ret = [];
      foreach ( $rs as $r )
         $ret [] = $this->format( $r );
      $ret = implode( $this->sep( self::SROW ), $ret );
      return $this->formatPart( self::ROWS, [$ret] );
   }

   /// parts rész kiírása
   protected function writeParts( $b ) {
      if ( ! $ps = $b->parts() ) return null;
      $save = $this->block;
      $ret = [];
      foreach ( $ps as $p ) {
         if ( $wp = $this->write($p) )
            $ret [] = $wp;
      }
      $this->block = $save;
      $ret = implode( $this->sep( self::SPART ), $ret );
      return $this->formatPart( self::PARTS, [$ret] );
   }

   /// egy rész formázása
   protected function formatPart( $part, $m ) {
      switch ( $part ) {
         case self::LINK:
            $lnk = Tools::g( $m, 2 );
            $this->refineLink( $m[1], $lnk );
            return $this->formatLink( $m[1], $lnk );
         case self::LST:
            return $this->formatList( $m[1] );
         default:
            return $m[0];
      }
   }

   /// link formázása
   protected function formatLink( $txt, $lnk ) {
      return sprintf("[%s](%s)", $txt, $lnk );
   }

   /// listaelem formázása
   protected function formatList( $txt ) {
      if ( $b = $this->get( Dox::BULLET ) )
         $txt = "$b $txt";
      return $txt;
   }

   /// egy link kiegészítése
   protected function refineLink( & $txt, & $lnk ) {
      if ( null == $lnk )
         $lnk = $txt;
      $i = strpos( $lnk, '#' );
      if ( 0 === $i && $txt == $lnk ) {
         $txt = substr($txt,1);
         return;
      }
      if ( preg_match('#:#', $lnk ) )
         return;
      if ( $h = $this->get( Dox::LINKHEAD ))
         $lnk = "$h$lnk";
      if ( $t = $this->get( Dox::LINKTAIL )) {
         if (false !== $i)
            $lnk = substr($lnk,0,$i).$t.substr($lnk,$i);
            else $lnk = "$lnk$t";
      }
   }

   /// sorok tördelése
   protected function wrap( $txt ) {
      if ( ! $w = $this->get( Dox::WRAP ))
         return $txt;
      $txt = trim($txt);
      $rows = explode("\n", $txt);
      $ret = [];
      foreach ( $rows as $r ) {
         $r = trim($r);
         if ( strlen($r) <= $w ) {
            $ret [] = $r;
         } else {
            while ( $r )
               $ret [] = $this->chopWrap( $r, $w );
         }
      }
      return implode("\n",$ret);
   }

   /// legrövidebb lehetséges rész levágása
   protected function chopWrap( &$r, $w ) {
      for ($i=$w; $i < strlen( $r ); ++$i ) {
         if ( ' ' == $r[$i] )
            break;
      }
      $ret = substr( $r, 0, $i );
      $r = trim(substr( $r, $i ));
      return $ret;
   }

}
