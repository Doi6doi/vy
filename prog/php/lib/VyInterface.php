<?php

/// interfész leíró fájl
class VyInterface
   implements VyExprReader
{

   const
      CONST = "const",
      EXTEND = "extend",
      FUNCTION = "function",
      IMPORT = "import",
      INTERFACE = "interface",
      PROVIDE = "provide",
      TYPE = "type";

   /// használt repo
   protected $repo;
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
   /// konstansok
   protected $consts;
   /// típusok
   protected $types;
   /// függvények
   protected $funcs;
   /// provide rész
   protected $provides;
   /// kifejezés olvasó verem
   protected $stack;

   function __construct() {
      $this->consts = [];
      $this->xtends = [];
      $this->imports = [];
      $this->types = [];
      $this->funcs = [];
      $this->provides = [];
      $this->stack = new VyStack( $this );
   }

   function name() { return $this->name; }

   /// teljes név útvonallal és verzióval
   function fullName() {
      return sprintf( "%s.%s%s", $this->pkg, $this->name, $this->ver );
   }

   /// fájl beolvasása
   function read( VyStream $s, VyRepo $repo ) {
      $this->repo = $repo;
      $this->stack->setStream( $s );
      $this->readHead( $s );
      while (true) {
         $s->readWS();
         if ( $s->readIf("}"))
            return;
         else
            $this->readPart( $s );
      }
   }

   /// típus olvasása
   function readType( $s ) {
      $s->readWS();
      $ret = $s->readIdent();
      $this->checkType( $ret );
      return $ret;
   }

   /// típus ellenőrzése
   function checkType( $type ) {
      if ( ! array_key_exists($type, $this->types) )
         throw new EVy("Unknown type: $type" );
//         $this->types[ $type ] = new VyInterfType($this);
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
         case self::CONST: $meth = "readConst"; break;
         case self::EXTEND: $meth = "readExtend"; break;
         case self::FUNCTION: $meth = "readFunction"; break;
         case self::IMPORT: $meth = "readImport"; break;
         case self::PROVIDE: $meth = "readProvide"; break;
         case self::TYPE: $meth = "readTypePart"; break;
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

   /// const elem olvasása
   protected function readConst( $s ) {
      $name = ($s->readIf("&") ? "&" : "").$s->readIdent();
      $s->readWS();
      $s->readToken(":");
      $type = $this->readType( $s );
      $this->add( $this->consts, $name, $type );
      $s->readWS();
      $s->readToken(";");
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
   protected function readTypePart( $s ) {
      $ret = new VyInterfType( $this );
      $ret->read( $s );
      $this->add( $this->types, $ret->name(), $ret );
   }

   /// függvény olvasása
   protected function readFunction( $s ) {
      $ret = new VyFunction( $this );
      $ret->read( $s );
      $this->add( $this->funcs, $ret->name(), $ret );
   }

   /// provide olvasása
   protected function readProvide( $s ) {
      $this->provides [] = $this->readExpr( $s );
      $s->readWS();
   }

   /// kifejezés olvasása
   protected function readExpr( $s ) {
      return $this->stack->readExpr();
   }

}
