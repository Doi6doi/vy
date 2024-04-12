<?php

namespace vy;

/// interfész leíró fájl
class Interf
   implements ExprCtx
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
      $this->stack = new Stack( $this );
   }

   function name() { return $this->name; }

   function types() { return $this->types; }

   function consts() { return $this->consts; }

   function funcs() { return $this->funcs; }

   /// teljes név útvonallal és verzióval
   function fullName() {
      return sprintf( "%s.%s%s", $this->pkg, $this->name, $this->ver );
   }

   /// fájl beolvasása
   function read( Stream $s, Repo $repo ) {
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
   }

   function __toString() { return $this->fullName(); }

   /// azonosító feloldás
   function resolve( $token, $kind ) {
      switch ($kind) {
         case ExprCtx::FUNC:
            foreach ( [$this->consts, $this->funcs] as $a ) {
               if ( $ret = Tools::g( $a, $token ))
                  return $ret;
            }
         break;
         case ExprCtx::INFIX:
            foreach ($this->funcs as $f) {
               if ( ($o = $f->oper()) && Oper::INFIX == $o->kind()
                  && $o->oper() == $token )
                  return $f;
            }
         break;
         default: throw new EVy("Unknown resolve kind: $kind");
      }
      return null;
   }

   /// típusok és függvények átvétele
   protected function inherit( $name, $o, $extend ) {
      foreach ( $o->types() as $t ) {
         if ( ! $tt = Tools::g( $this->types, $t->name() ))
            $tt = new InterfType( $this, $t );
         $tt->add( $name.".".$t );
      }
      if ( $extend ) {
         foreach ( $o->consts() as $c )
            $this->add( $this->consts, $c->name(), $c );
         foreach ( $o->funcs() as $f )
            $this->add( $this->funcs, $f->name(), $f );
      }
   }

   /// fejrész beolvasása
   protected function readHead( $s ) {
      $s->readWS();
      $s->readToken( self::INTERFACE );
      $s->readWS();
      $path = $s->readPath();
      $this->name = array_pop( $path );
Tools::debug("READING ".$this->name());
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

   /// hozzáadás egy listához
   protected function add( & $arr, $name, $obj ) {
      $arrs = $this->addArrays( $arr );
      foreach ( $arrs as $a ) {
         if ( array_key_exists( $name, $a ))
            throw new EVy("Duplicate name: $name");
      }
      $arr[$name] = $obj;
   }

   /// egy kategóriába eső tömbök
   protected function addArrays( $arr ) {
      switch ( $arr ) {
         case $this->consts: case $this->funcs:
            return [$this->consts, $this->funcs];
         case $this->xtends: case $this->imports:
            return [$this->xtends, $this->imports];
         default:
            return [$arr];
      }
   }

   /// függvény olvasása
   protected function readConst( $s ) {
      $ret = new Func( $this );
      $ret->readConst( $s );
      $this->add( $this->consts, $ret->name(), $ret );
   }


   /// extend elem olvasása
   protected function readExtend( $s ) {
      $this->readAlias( $s, $name, $path, $ver );
      $ret = $this->repo->force( $path, $ver );
      $this->add( $this->xtends, $name, $ret );
      $s->readWS();
      $s->readToken(";");
      $this->inherit( $name, $ret, true );
   }

   /// alias olvasása
   protected function readAlias( $s, & $name, & $path, & $ver ) {
      $name = $s->readIdent();
      $s->readWS();
      $path = [];
      if ( ($alias = $s->readIf("="))
            || $s->readIf("."))
         $path = $s->readPath();
      if ( ! $alias ) {
         array_unshift( $path, $name );
         $name = $path[ count($path)-1 ];
      }
      if (1 == count($path))
         $path = sprintf( "%s.%s", $this->pkg, $path[0] );
         else $path = implode(".",$path);
      if ( "@" == $s->next() )
         $ver = $s->readVerCond();
         else $ver = "@<=".substr($this->ver,1);
   }

   /// import elem olvasása
   protected function readImport( $s ) {
      $this->readAlias( $s, $name, $path, $ver );
      $ret = $this->repo->force( $path, $ver );
      $this->add( $this->imports, $name, $ret );
      $s->readWS();
      $s->readToken(";");
      $this->inherit( $name, $ret, false );
   }

   /// type elem olvasása
   protected function readTypePart( $s ) {
      $ret = new InterfType( $this );
      $ret->read( $s );
      $this->add( $this->types, $ret->name(), $ret );
   }

   /// függvény olvasása
   protected function readFunction( $s ) {
      $ret = new Func( $this );
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
