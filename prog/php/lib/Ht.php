<?php

namespace vy;

/// html készítő osztály
class Ht {

   /// div
   static function div( $part, $pars=null ) {
      if ( $pars ) {
         if ( ! is_array( $pars ))
            $pars = ["class"=>$pars];
      }
      return self::elem( "div", $pars, $part );
   }

   /// link
   static function a( $href, $part ) {
      return self::elem( "a", ["href"=>$href], $part );
   }

   /// törés
   static function br() {
      return self::elem( "br" )."\n";
   }

   /// lista
   static function ul( $items, $pars=null ) {
      $part = "";
      if ( is_array( $items )) {
         foreach ( $items as $i )
            $part .= self::li( $i )."\n";
      } else {
        $part = $items;
      }
      return self::elem( "ul", $pars, $part );
   }

   /// listaelem
   static function li( $item, $pars=null ) {
      return self::elem( "li", $pars, $item );
   }

   /// paraméteres elem
   static function elem( $tag, $pars=null, $part=null ) {
      $ret = "<$tag";
      if ( $pars ) {
         foreach ( $pars as $k=>$v )
           $ret .= " $k=".'"'.htmlspecialchars( $v ).'"';
      }
      if ( $part )
         $ret .= ">".$part."</$tag>";
         else $ret .= "/>";
      return $ret;
   }

   /// html escape-elés
   static function escape( $s ) {
      $ret = htmlspecialchars( $s );
      $ret = str_replace( [" ","\n"],
         ["&nbsp;",self::br()], $ret );
      return $ret;
   }


}
