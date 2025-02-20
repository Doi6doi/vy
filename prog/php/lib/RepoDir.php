<?php

namespace vy;

/// könyvtár repository
class RepoDir extends Repo {

   protected $root;

   function __construct( $root ) {
      parent::__construct();
      $this->root = $root;
   }

   function contains( $x, Version $ver ) {
      return parent::contains( $x, $ver )
         || null != $this->find( $x, $ver );
   }

   function force( $x, $ver ) {
      if ( $ret = $this->findObj( $x, $ver ) )
         return $ret;
      if ( ! $fname = $this->find( $x, $ver ))
         throw new EVy("Cannot find: $x$ver");
      if ( preg_match('#^(.*)(@\d+)\.vy$#', $fname, $m )) {
         $v = Version::parse( $m[2], true );
         if ( $ret = $this->findObj( Tools::dirPkg($m[1]), $v ))
            return $ret;
      }
      $fname = $this->root."/".$fname;
      return $this->readStream( new ExprStream($fname) );
   }

   function read( $x, $ver ) {
      throw new EVy("Should not be called");
   }

   /// legjobb fájl megkeresése
   function find( $x, Version $cond ) {
      if ( ! preg_match('#^(.+)\.([^.]+)$#', $x, $m ))
         return false;
      $path = $m[1];
      $name = $m[2];
      $pdir = Tools::pkgDir($path);
      $dir = $this->root."/".$pdir;
      if ( ! is_dir( $dir ))
         return false;
      $best = null;
      foreach ( glob("$dir/$name@*.vy") as $f ) {
         if ( preg_match('#^.+(@\d+)\.vy$#', $f, $n)) {
            if ( ($c = Version::parse( $n[1], false ))
               && $c->matches( $cond ))
            {
               if ( ! $best || $best->day() <= $c->day() )
                  $best = $c;
            }
         }
      }
      return $best ? "$pdir/$name@".$best->num().".vy" : null;
   }





}
