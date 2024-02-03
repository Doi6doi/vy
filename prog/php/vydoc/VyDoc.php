<?php

/// vy document reader
class VyDoc {

   const
      CONFIG = "vydoc.conf";

   const
      PAGE = "page";

   const
      BASE = "base",
      CSS = "css",
      MAIN = "main";

   protected $conf;
   protected $path;

   function __construct() {
      $this->loadConfig();
   }

   /// megjelenítő futtatása
   function run( $req ) {
      try {
         $page = Tools::g( $req, self::PAGE );
         if ( ! $page )
            $page = $this->conf( self::MAIN );
         $this->show( $page );
      } catch (Throwable $e) {
         $this->error( $e );
      } catch (Exception $e) { // PHP5 compat
         $this->error( $e );
      }
   }

   /// egy oldal megjelenítése
   function show( $page ) {
      if ( ! $fname = $this->find( $page ) )
         throw new EVy("Cannot find $page");
      $vp = VyPage::create( $this->base(), $fname );
      $this->path = $vp->path();
      $this->printHtml( $vp->html() );
   }

   /// egy konfigurációs érték
   function conf( $field ) {
      return Tools::g( $this->config, $field );
   }

   /// hibaüzenet kiírása
   function error( $e ) {
      $msg = sprintf( "## %s(%s): %s",
         $e->getFile(), $e->getLine(), $e->getMessage() );
      $arr = explode("\n", $e->getTraceAsString());
      array_unshift( $arr, $msg );
      foreach ( $arr as & $a )
         $a = Ht::escape( $a );
      $err = Ht::div( implode(Ht::br(), $arr ), "error" );
      $this->printHtml($err);
   }

   /// konfiguráció betöltése
   function loadConfig() {
      $data = Tools::loadFile( self::CONFIG );
      $this->config = Tools::jsonDecode( $data );
   }

   /// html oldal kiírása
   function printHtml( $html ) {
      $this->printHead();
      print( $html );
      $this->printTail();
   }

   /// alapkönyvtár
   function base() {
      $ret = $this->conf( self::BASE );
      return $ret ? $ret : ".";
   }

   /// oldal fájl megkeresése
   function find( $page ) {
      if ( ! $this->parse( $page, $path, $name, $ver ) )
         throw new EVy("Unknown page format: '$page'");
      if ( ! $path )
         $path = $this->path;
      $path = $this->base()."/".$path;
      return $this->findAt( $path, $name, $ver );
   }

   /// oldal fájl megkeresése egy könyvtárban
   function findAt( $path, $name, $ver ) {
      $pn = "$path/$name";
      if ( is_dir( $pn ))
         return $pn;
      if ( false === ($arr = glob( "$path/$name*.vy")))
         throw new EVy("Unkown path: $path");
      for ( $i = count($arr)-1; 0 <= $i; --$i) {
         $ai = $arr[$i];
         if ( preg_match('#^.*/([^/@]+)(@\d+)?\.vy$#', $ai, $m )) {
            if ( $m[1] == $name && (
               !$ver || $ver >= Tools::g( $m, 2 )
            ))
               return $ai;
         }
      }
      return false;
   }

   /// elérési út felbontása
   function parse( $txt, & $path, & $name, & $ver ) {
      if ( ! preg_match('#^([a-zA-Z_0-9.]+\.)?([a-zA-Z_0-9]+)(@\d+)?$#', $txt, $m ))
         return false;
      $path = str_replace( ".", "/", Tools::g( $m, 1 ) );
      $name = $m[2];
      $ver = Tools::g( $m, 3 );
      return true;
   }

   /// html fejléc kiírása
   function printHead() {
      $arr = [
         '<html>',
         '<head>',
         '<meta charset="UTF-8">',
         '<meta name="viewport" content="width=device-width, initial-scale=1" />',
         '<title>VyDoc</title>',
         '<link rel="stylesheet" type="text/css" href="'.$this->conf( self::CSS ).'" />',
         '</head>',
         '<body>'
      ];
      print( implode("\n", $arr ));
   }

   /// html befejezés kiírása
   function printTail() {
      $arr = [
         "</body>",
         "</html>"
      ];
      return implode("\n", $arr);
   }

}

