<?php

/// interfész leíró fájl
class VyInterface {

   const
      EXTEND = "extend",
      IMPORT = "import",
      TYPE = "type",
      INTERFACE = "interface";

   /// csomag
   protected $pkg;
   /// név
   protected $name;
   /// verzió
   protected $ver;
   /// ősinterfészek
   protected $xtends;
   /// importok
   protected $imports;
   /// típusok
   protected $types;
   /// használt repo
   protected $repo;

   function __construct() {
      $this->xtends = [];
      $this->imports = [];
      $this->types = [];
   }

   function name() { return $this->name; }

   /// teljes név útvonallal és verzióval
   function fullName() {
      return sprintf( "%s.%s%s", $this->pkg, $this->name, $this->ver );
   }

   /// fájl beolvasása
   function read( VyStream $s, VyRepo $repo ) {
      $this->repo = $repo;
      $this->readHead( $s );
      while (true) {
         $s->readWS();
         if ( $s->readIf("}"))
            return;
         else
            $this->readPart( $s );
      }
   }

   /// fejrész beolvasása
   protected function readHead( $s ) {
      $s->readWS();
      $s->readToken( self::INTERFACE );
      $s->readWS();
      $path = $s->readPath();
      $this->name = array_pop( $path );
      $this->pkg = implode(".",$path);
      $s->readWS();
      $this->ver = $s->readVer();
      $this->repo->addObj( $this->fullName(), $this );
      $s->readWS();
      $s->readToken("{");
   }

   /// egy rész olvasása
   protected function readPart( $s ) {
      $s->readWS();
      switch ( $n = $s->next() ) {
         case self::EXTEND: $meth = "readExtend"; break;
         case self::IMPORT: $meth = "readImport"; break;
         case self::TYPE: $meth = "readType"; break;
         default: throw new Exception("Unknown part: $n");
      }
      return $this->readPartBlock( $s, $meth );
   }

   /// részblokk olvasása
   protected function readPartBlock( $s, $meth ) {
      $s->read();
      $s->readWS();
      $clb = [$this,$meth];
      if ( ! $s->readIf("{")) {
         call_user_func( $clb, $s );
      } else {
         while (true) {
            $s->readWS();
            if ( $s->readIf("}"))
               return;
            call_user_func( $clb, $s );
         }
      }
      return true;
   }

   protected function add( & $arr, $name, $obj ) {
      if ( array_key_exists( $name, $arr ))
         throw new EVy("Duplicate name: $name");
      $arr[$name] = $obj;
   }

   /// extend elem olvasása
   protected function readExtend( $s ) {
      $this->readPathVer( $s, $path, $ver );
      $obj = $this->repo->force( $path, $ver );
      $this->add( $this->xtends, $obj->name(), $obj );
      $s->readWS();
      $s->readToken(";");
   }

   /// útvonal és verzió feltétel olvasása
   protected function readPathVer( $s, & $path, & $ver ) {
      $path = $s->readPath();
      if (1 == count($path))
         $path = sprintf( "%s.%s", $this->pkg, $path[0] );
         else $path = implode(".",$path);
      if ( "@" == $s->next() )
         $ver = $s->readVerCond();
         else $ver = "@<=".substr($this->ver,1);
   }

   /// import elem olvasása
   protected function readImport( $s ) {
      $this->readPathVer( $s, $path, $ver );
      $obj = $this->repo->force( $path, $ver );
      $this->add( $this->imports, $obj->name(), $obj );
   }

   /// type elem olvasása
   protected function readType( $s ) {
      $ret = new VyInterfType( $this, $s->readIdent() );
      $this->add( $this->types, $ret->name(), $ret );
      $ret->readDetails( $s );
   }

}
