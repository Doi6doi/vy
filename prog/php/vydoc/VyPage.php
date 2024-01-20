<?php

require_once( "../parsedown/Parsedown.php" );

/// dokumentáció oldal
class VyPage {

   protected $path;
   protected $name;
   protected $ver;

   protected $md;

   function __construct( $fname ) {
      $this->read( new VyStream($fname) );
   }

   function read( VyStream $s ) {
      $s->readWS();
      switch ( $n = $s->next() ) {
         case "informal": return $this->readInformal( $s );
         default: throw new EVy("Unknown vy file: $n");
      }
   }

   function path() { return $this->path; }

   function readInformal( VyStream $s ) {
      $s->readToken("informal");
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
      $s->readToken("{");
      $md = trim( $s->readAll() );
      if ( "}" == $md[ strlen($md)-1 ] )
         $md = substr( $md, 0, strlen( $md )-1 );
      $md = preg_replace_callback( '%\(#(.*?)\)%', [$this, "replaceLink"], $md );
      $this->md = $md;
   }

   function replaceLink( $matches ) {
      $m1 = $matches[1];
      if ( ! preg_match('#\.#', $m1 )) 
         $m1 = $this->path.".".$m1;
      return "(?".VyDoc::PAGE."=$m1)";
   }

   function html() {
      $pd = new Parsedown();
      return $pd->text( $this->md );
   }

}

