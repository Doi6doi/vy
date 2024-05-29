<?php

namespace vy;

/// vy fájlok betöltő helye
abstract class Repo {

   static function create( $repo ) {
      if ( preg_match('#^([a-z]+)://(.+)$#', $repo, $m ))
         return self::createUri( $m[1], $m[2] );
         else return self::createUri( "file", $repo );
   }

   static function createUri( $schema, $value ) {
      switch ( $schema ) {
         case "file": return new RepoDir( $value );
         default: throw new EVy("Unknown schema: $schema");
      }
   }


   protected $objs;

   function __construct() {
      $this->objs = [];
   }

   /// hozzadás a tárhoz
   function addObj( $full, $obj ) {
      if ( array_key_exists($full, $this->objs))
         throw new EVy("Duplicate name: $full");
      $this->objs[$full] = $obj;
   }

   /// tartalmazza-e a repo a csomagot
   function contains( $x, $ver ) {
      return null != $this->findObj( $x, $ver );
   }

   /// csomag kikérése
   function force( $x, $ver ) {
      if ( $ret = $this->findObj( $x, $ver ))
         return $ret;
      return $this->read( $x, $ver );
   }

   /// keresés a tárban
   function findObj( $x, $ver ) {
      return Tools::g( $this->objs, $x.$ver );
   }

   /// csomag beolvasása
   protected function read( $x, $ver ) {
      throw new EVy("Not implemented: ".get_class($this).".read");
   }

   /// stream olvasása
   protected function readStream( ExprStream $s ) {
      try {
         $s->readWS();
         switch ( $n = $s->next() ) {
            case Interf::INTERFACE: $ret = new Interf(); break;
            throw new EVy("Unknown vy file: $n");
         }
         $ret->read( $s, $this );
         return $ret;
      } catch ( \Exception $e ) {
         throw new EVy( $s->position().": ".$e->getMessage(), 0, $e );
      }
   }

}
