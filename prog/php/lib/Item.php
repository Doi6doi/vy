<?php

namespace vy;

/// vy neves és verziós elem
abstract class Item
   implements RepoItem, ExprCtx
{

   /// kulcsszavak
   const
      CONST = "const",
      EXTEND = "extend",
      FUNCTION = "function",
      METHOD = "method",
      IMPORT = "import",
      PROVIDE = "provide",
      TYPE = "type";

   /// zászlók
   const
      NODEF = "nodef";

   /// használt repo
   protected $repo;
   /// csomag
   protected $pkg;
   /// név
   protected $name;
   /// verzió
   protected $ver;
   /// zászlók
   protected $flags;
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
   /// az örökölt függvények már átvéve
   protected $funcsTaken;

   function __construct() {
      $this->flags = [];
      $this->consts = [];
      $this->xtends = [];
      $this->imports = [];
      $this->types = [];
      $this->funcs = [];
      $this->provides = new Conds( $this );
      $this->stack = new Stack( $this );
   }

   abstract function className();

   function name() { return $this->name; }

   function pkg() { return $this->pkg; }

   function pkgName() { return $this->pkg.".".$this->name; }

   function types() { return $this->types; }

   function consts() { return $this->consts; }

   function funcs() { return $this->funcs; }

   function ver() { return $this->ver; }

   function has($flag) {
      return array_key_exists( $flag, $this->flags );
   }

   /// alapértelmezett típus
   function defType() {
      if ( $this->has( self::NODEF ))
         return null;
         return $this->name;
   }

   /// teljes név útvonallal és verzióval
   function fullName() {
      return $this->pkgName()."@".$this->ver->day();
   }

   /// fájl beolvasása
   function read( ExprStream $s, Repo $repo ) {
      $this->repo = $repo;
      $this->readHead( $s );
      while (true) {
         $s->readWS();
         if ( $s->readIf("}"))
            break;
         else
            $this->readPart( $s );
      }
      $this->inheritFuncs();
   }
   
   /// típus olvasása
   function readType( $s ) {
      $s->readWS();
      $ret = $s->readIdent();
      $this->checkType( $ret );
      return $ret;
   }

   /// típus kivétele egy névből
   function removeType( $type ) {
      if ( ! preg_match('#^(.*)\.([^.]+)$#', $type, $m )) return;
      if ( ! $inf = $this->resolve( $m[1], ExprCtx::INTF ))
         throw new EVy("Unknown interface: $inf");
      $t = $m[2];
      $inf->checkType( $t );
      if ( $ret = Tools::g( $this->types, $t )) {
         if ( $ret->remove($type) && ! $ret->same() )
            unset( $this->types[$t] );
      }
   }

   /// típus ellenőrzése
   function checkType( $type ) {
      if ( ! array_key_exists($type, $this->types) )
         throw new EVy("Unknown type: $type" );
   }

   /// hívható-e 
   function canCall( $x ) { return true; }

   function __toString() { return $this->fullName(); }

   /// azonosító feloldás
   function resolve( $token, $kind ) {
      $arrs = [];
      switch ($kind) {
         case ExprCtx::FUNC:
         case ExprCtx::NAME:
            $arrs = [$this->consts, $this->funcs];
         break;
         case ExprCtx::INTF:
            $arrs = [$this->xtends, $this->imports];
         break;
         case ExprCtx::INFIX:
            foreach ($this->funcs as $f) {
               if ( ($o = $f->oper()) && Oper::IN == $o->kind()
                  && $o->oper() == $token )
                  return $f;
            }
            return null;
         break;
         default: throw new EVy("Unknown resolve kind: $kind");
      }
      foreach ( $arrs as $a ) {
         if ( $ret = Tools::g( $a, $token )) {
            return $ret;
         }
      }
      return null;
   }

   /// típusok és függvények átvétele
   protected function inheritTypes( $name, $o ) {
      foreach ( $o->types() as $t ) {
         $tn = $t->name();
         if ( ! $tt = Tools::g( $this->types, $tn )) {
            $tt = new ItemType( $this, $tn );
            $this->add( $this->types, $tn, $tt );
         }
         $tt->add( $name.".".$tn );
      }
   }

   /// függvények átvétele
   protected function inheritFuncs() {
      if ( $this->funcsTaken ) return;
      $map = $this->typeMap();
      foreach ( $this->xtends as $x ) {
         foreach ( $x->consts() as $c )
            $this->inheritFunc( $c, $map );
         foreach ( $x->funcs() as $f )
            $this->inheritFunc( $f, $map );
      }
      $this->funcsTaken = true;
   }

   /// fejrész beolvasása
   protected function readHead( $s ) {
      $s->readWS();
      $s->readToken( $this->className() );
      $s->readWS();
      $path = $s->readIdents(".");
      $this->name = array_pop( $path );
      $this->pkg = implode(".",$path);
      $s->readWS();
      $this->ver = Version::read( $s );
      $this->repo->addItem( $this );
      $s->readWS();
      $s->readToken("{");
      $this->addSelfType();
   }

   /// egy rész olvasása
   protected function readPart( $s ) {
      $s->readWS();
      switch ( $n = $s->next() ) {
         case self::CONST: $meth = "readConst"; break;
         case self::EXTEND: $meth = "readExtend"; break;
         case self::FUNCTION: $meth = "readFunction"; break;
         case self::IMPORT: $meth = "readImport"; break;
         case self::METHOD: $meth = "readMethod"; break;
         case self::PROVIDE:
            $meth = "readProvide";
            $this->inheritFuncs();
         break;
         case self::TYPE: $meth = "readTypePart"; break;
         default: throw new EVy("Unknown part: $n");
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
      $ret = new ItemConst( $this, $this->defType() );
      $ret->read( $s );
      $this->add( $this->consts, $ret->name(), $ret );
   }

   /// extend elem olvasása
   protected function readExtend( $s ) {
      $this->readAlias( $s, $name, $path, $ver );
      $ret = $this->repo->force( $path, $ver );
      $this->add( $this->xtends, $name, $ret );
      $s->readWS();
      $s->readToken(";");
      $this->inheritTypes( $name, $ret );
      $this->funcsTaken = false;
   }

   /// alias olvasása
   protected function readAlias( $s, & $name, & $path, & $ver ) {
      $name = $s->readIdent();
      $s->readWS();
      $path = [];
      if ( $alias = $s->readIf("=")) {
         $s->readWS();
         $path = $s->readIdents(".");
      } else if ( $s->readIf(".")) {
         $path = $s->readIdents(".");
      }
      if ( ! $alias ) {
         array_unshift( $path, $name );
         $name = $path[ count($path)-1 ];
      }
      if (1 == count($path))
         $path = sprintf( "%s.%s", $this->pkg, $path[0] );
         else $path = implode(".",$path);
      if ( "@" == $s->next() )
         $ver = Version::read( $s, true );
         else $ver = new Version( $this->ver->num(), "<=" );
   }

   /// import elem olvasása
   protected function readImport( $s ) {
      $this->readAlias( $s, $name, $path, $ver );
      $ret = $this->repo->force( $path, $ver );
      $this->add( $this->imports, $name, $ret );
      $s->readWS();
      $s->readToken(";");
      $this->inheritTypes( $name, $ret );
   }

   /// type elem olvasása
   protected function readTypePart( $s ) {
      $ret = new ItemType( $this );
      $ret->read( $s );
      $name = $ret->name();
      if ( $old = Tools::g( $this->types, $name ))
         $old->append( $ret );
         else $this->add( $this->types, $name, $ret );
      $ret->updateInterf();
   }

   /// függvény olvasása
   protected function readFunction( $s ) {
      $ret = new ItemFunction( $this, $this->defType() );
      $ret->read( $s );
      $this->add( $this->funcs, $ret->name(), $ret );
   }

   /// metódus olvasása
   protected function readMethod( $s ) {
      $ret = new ItemMethod( $this, $this->defType() );
      $ret->read( $s );
      $this->add( $this->funcs, $ret->name(), $ret );
   }

   /// provide olvasása
   protected function readProvide( $s ) {
      $this->provides->readPart( $s );
   }

   /// kifejezés olvasása
   protected function readExpr( $s ) {
      return $s->readExpr();
   }

   /// típusok kiírása
   protected function dumpTypes() {
      $ret = [];
      foreach ( $this->types as $t )
         $ret [] = "".$t;
      return implode(";",$ret);
   }

   /// függvény öröklése
   protected function inheritFunc( ItemFunc $f, $map ) {
      $name = $f->name();
      $fmap = $this->mapSlice( $map, $f->owner()->name() );
      if ( $g = Tools::g( $this->funcs, $name ) ) {
         $g->checkCompatible( $f, $fmap );
      } else {
         $g = ItemFunc::create( $this, $f );
         $g->inherit( $f, $fmap );
         $this->funcs[ $name ] = $g;
      }
   }

   /// típusmegfeleltetési térkép
   protected function typeMap() {
      $ret = [];
      foreach ( $this->types as $t ) {
         foreach ( $t->same() as $s )
            $ret[$s] = $t->name();
      }
      return $ret;
   }

   /// térkép egy része
   protected function mapSlice( $map, $intf ) {
      $ret = [];
      $intf = $intf.".";
      $li = strlen( $intf );
      foreach ( $map as $k=>$v) {
         if ( $intf == substr( $k, 0, $li ))
            $ret[ substr($k,$li) ] = $v;
      }
      return $ret;
   }

   /// saját típus hozzáadása
   protected function addSelfType() {
      $n = $this->name();
      $t = new ItemType( $this, $n );
      $this->add( $this->types, $n, $t );
   }

}
