<?php

namespace vy;

/// dokumentáció könyvtár oldal
class PageDir {

   protected $base;
   protected $path;

   protected $items;

   function __construct( $base, $path ) {
      $this->base = $base;
      $bl = strlen( $base );
      $path = preg_replace( "#/+#", "/", $path );
      if ( substr( $path, 0, $bl ) == $base )
         $path = substr( $path, $bl );
      while ( "/" == substr( $path, 0, 1 ))
         $path = substr( $path, 1 );
      $this->path = $path;
      $this->build();
      $this->path = str_replace( "/",".", $this->path );
   }

   function path() {
      return str_replace( ".","/", $this->path );
   }

   /// elemek építése
   function build() {
      $this->items = [];
      $ptn = sprintf( "%s/%s/*", $this->base, $this->path );
      foreach ( glob( $ptn ) as $f ) {
         if ( preg_match('#^(.*)/(.+?)(@\d+)?\.vy$#', $f, $m )) {
            $name = $m[2];
            $ver = Tools::g( $m, 3 );
            if ( $this->better( $name, $ver ))
               $this->items[$name] = $ver;
         }
      }
   }

   /// az érkező verzió jobb-e, mint a korábbi
   function better( $name, $ver ) {
      if ( ! $ov = Tools::g( $this->items, $name ))
         return true;
      return $ov < $ver;
   }

   function title() {
      return Ht::div( $this->path, "title" );
   }

   function item( $name, $ver ) {
      $href = sprintf( "?%s=%s.%s", Doc::PAGE, $this->path, $name );
      return Ht::a( $href, $name );
   }

   function html() {
      $ret = [];
      foreach ( $this->items as $k=>$v )
         $ret [] = $this->item( $k, $v );
      $ret = $this->title().Ht::ul( $ret );
      return $ret;
   }

}

