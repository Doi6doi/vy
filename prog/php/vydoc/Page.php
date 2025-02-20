<?php

namespace vy;

require_once( "../parsedown/Parsedown.php" );

/// dokumentáció oldal
class Page {

   const
      INFORMAL = "informal",
      SINTERFACE = "interface";

   protected $kind;
   protected $path;
   protected $name;
   protected $ver;
   protected $rows;

   protected $md;

   static function create( $base, $fname ) {
      if ( is_dir( $fname ))
         return new PageDir( $base, $fname );
         else return new Page( $fname );
   }

   function __construct( $fname ) {
      $this->read( new Stream($fname) );
   }

   function read( Stream $s ) {
      $s->readWS();
      switch ( $n = $this->kind = $s->next() ) {
         case self::INFORMAL: return $this->readInformal( $s );
         case self::SINTERFACE: return $this->readRows( $s, self::SINTERFACE );
         default: throw new EVy("Unknown vy file: $n");
      }
   }

   function path() { return $this->path; }

   function readHead( Stream $s, $kind ) {
      $s->readToken( $kind );
      $s->readWS();
      $this->name = $s->readIdent();
      while ( $s->readIf(".") ) {
         $this->path .= ($this->path?"/":"").$this->name;
         $this->name = $s->readIdent();
      }
      $s->readWS();
      if ( "@" == $s->next())
         $this->ver = Version::read( $s );
      $s->readWS();
   }

   function readInformal( Stream $s ) {
      $this->readHead( $s, self::INFORMAL );
      $s->readToken("{");
      $md = trim( $s->readAll() );
      if ( "}" == $md[ strlen($md)-1 ] )
         $md = substr( $md, 0, strlen( $md )-1 );
      $md = preg_replace_callback( '%\(#(.*?)\)%', [$this, "replaceLink"], $md );
      $this->md = $md;
   }

   /// sorok olvasása
   function readRows( Stream $s, $kind ) {
      $this->readHead( $s, $kind );
      $this->rows = [];
      while ( ! $s->eos() )
         $this->rows [] = $this->readLine( $s );
   }

   /// egy sor olvasása
   function readLine( Stream $s ) {
      $ret = "";
      while ( null !== ( $r = $s->read()) ) {
         $ret .= $r;
         if ( 0 <= strpos($r, "\n"))
            break;
      }
      return $ret;
   }

   function replaceLink( $matches ) {
      $m1 = $matches[1];
      if ( ! preg_match('#\.#', $m1 ))
         $m1 = $this->path.".".$m1;
      return "(?".Doc::PAGE."=$m1)";
   }

   function html() {
      switch ( $this->kind ) {
         case self::INFORMAL: return $this->htmlMd();
         default: return $this->htmlRows();
      }
   }

   function htmlMd() {
      $pd = new \Parsedown();
      return $pd->text( $this->md );
   }

   function htmlRows() {
      $ret = [$this->htmlHead()];
      foreach ($this->rows as $r)
         $ret [] = Ht::escape( $r );
      return Ht::div( implode( "", $ret), "code" );
   }

   function htmlHead() {
      $pre = str_replace( "/", ".", $this->path );
      $pre = $pre ? "$pre." : "";
      $head = sprintf( "%s %s%s %s ", $this->kind, $pre, $this->name, $this->ver );
      return Ht::escape( $head );
   }


}

