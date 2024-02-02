<?php

require_once( "../parsedown/Parsedown.php" );

/// dokumentáció oldal
class VyPage {

   const
      INFORMAL = "informal",
      INTERFACE = "interface";

   protected $kind;
   protected $path;
   protected $name;
   protected $ver;
   protected $rows;

   protected $md;

   static function create( $base, $fname ) {
      if ( is_dir( $fname ))
         return new VyPageDir( $base, $fname );
         else return new VyPage( $fname );
   }

   function __construct( $fname ) {
      $this->read( new VyStream($fname) );
   }

   function read( VyStream $s ) {
      $s->readWS();
      switch ( $n = $this->kind = $s->next() ) {
         case self::INFORMAL: return $this->readInformal( $s );
         case self::INTERFACE: return $this->readRows( $s, self::INTERFACE );
         default: throw new EVy("Unknown vy file: $n");
      }
   }

   function path() { return $this->path; }

   function readHead( VyStream $s, $kind ) {
      $s->readToken( $kind );
      $s->readWS();
      $this->name = $s->readIdent();
      while ( $s->readIf(".") ) {
         $this->path .= ($this->path?"/":"").$this->name;
         $this->name = $s->readIdent();
      }
      $s->readWS();
      if ( "@" == $s->next())
         $this->ver = $s->readVer();
      $s->readWS();
   }

   function readInformal( VyStream $s ) {
      $this->readHead( $s, self::INFORMAL );
      $s->readToken("{");
      $md = trim( $s->readAll() );
      if ( "}" == $md[ strlen($md)-1 ] )
         $md = substr( $md, 0, strlen( $md )-1 );
      $md = preg_replace_callback( '%\(#(.*?)\)%', [$this, "replaceLink"], $md );
      $this->md = $md;
   }

   function readRows( VyStream $s, $kind ) {
      $this->readHead( $s, $kind );
      $this->rows = [];
      while ( ! $s->eos() )
         $this->rows [] = $s->readLine();
   }


   function replaceLink( $matches ) {
      $m1 = $matches[1];
      if ( ! preg_match('#\.#', $m1 ))
         $m1 = $this->path.".".$m1;
      return "(?".VyDoc::PAGE."=$m1)";
   }

   function html() {
      switch ( $kind ) {
         case self::INFOMRAL: return $this->htmlMd();
         default: return $this->htmlRows();
      }
   }

   function htmlMd() {
      $pd = new Parsedown();
      return $pd->text( $this->md );
   }

   function htmlRows() {
      $ret = "";
      foreach ($this->rows as $r)
         $ret .= htmlspecialchars( $r ).Ht::br();
      return Ht::div( $ret, "rows" );
   }


}

