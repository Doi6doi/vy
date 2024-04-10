<?php

/// vy fájlok betöltő helye
abstract class VyRepo {

   static function create( $repo ) {
      if ( preg_match('#^([a-z]+)://(.+)$#', $repo, $m ))
         return self::createUri( $m[1], $m[2] );
         else return self::createUri( "file", $repo );
   }

   static function createUri( $schema, $value ) {
      switch ( $schema ) {
         case "file": return new VyRepoDir( $value );
         default: throw new Exception("Unknown schema: $schema");
      }
   }

   abstract function contains( $i, $ver );

}
