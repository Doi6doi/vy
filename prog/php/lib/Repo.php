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
   function addItem( RepoItem $item ) {
      $n = $item->pkgName();
      $v = $item->ver();
      if ( ! $g = Tools::g( $this->objs, $n ))
         $this->objs[$n] = [];
      if ( Tools::g( $this->objs, $v->day() ))
         throw new EVy("Duplicate item: $n$v");
      $this->objs[$n][$v->day()] = $item;
   }

   /// elem kényszerítése
   function force( $pkgName, Version $ver ) {
      if ( $ret = $this->find( $pkgName, $ver ))
         return $ret;
      if ( $ret = $this->read( $pkgName, $ver ))
         return $ret;
      throw new EVy("Cannot find $pkgName$ver");
   }

   /// elem beolvasása
   function read( $pkgName, Version $ver ) { return null; }

   /// keresés a tárban
   function find( $pkgName, Version $ver ) {
      $ret = null;   
      if ( $g = Tools::g( $this->objs, $pkgName )) {
         foreach ($g as $o) {
            if ( Version::better( $o->ver(), $ver, $ret ))
               $ret = $o;
         }
      }
      return $ret;
   }

   /// stream olvasása
   protected function readStream( ExprStream $s ) {
      try {
         $s->readWS();
         switch ( $n = $s->next() ) {
            case Interf::INTERFACE: $ret = new Interf(); break;
            case Cls::CLS: $ret = new Cls(); break;
            default: throw new EVy("Unknown vy file: $n");
         }
         $ret->read( $s, $this );
         return $ret;
      } catch ( \Exception $e ) {
         throw new EVy( $s->position().": ".$e->getMessage(), 0, $e );
      }
   }

}
