<?php

/// html készítő osztály
class Ht {

   /// div
   static function div( $part, $pars=null ) {
      if ( $pars ) {
         if ( ! is_array( $pars ))
            $pars = ["class"=>$pars];
      }
      return self::paramed( "div", $pars, $part );
   }

   /// link
   static function a( $href, $part ) {
      return self::paramed( "a", ["href"=>$href], $part );
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
      return self::paramed( "ul", $pars, $part );
   }

   /// listaelem
   static function li( $item, $pars=null ) {
      return self::paramed( "li", $pars, $item );
   }

   /// paraméteres elem
   static function paramed( $tag, $pars, $part ) {
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


}
