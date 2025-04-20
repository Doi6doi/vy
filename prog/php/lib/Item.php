<?php

namespace vy;

/// vy neves és verziós elem
abstract class Item
   implements RepoItem, ExprCtx, Expr
{

   /// kulcsszavak
   const
      CONST = "const",
      EXTEND = "extend",
      FIELD = "field",
      FLAG = "~",
      FUNCTION = "function",
      METHOD = "method",
      IMPORT = "import",
      PROVIDE = "provide",
      TYPE = "type";

   /// zászlók
   const
      NODEF = "nodef";
   const
      ALLFLAGS = [self::NODEF];

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
   /// típusok
   protected $types;
   /// mezők
   protected $fields;
   /// függvények
   protected $funcs;
   /// provide rész
   protected $provides;
   /// kifejezés olvasó verem
   protected $stack;

   /// az örökölt függvények már átvéve
   protected $funcsTaken;
   /// az örökölt mezők már átvéve
   protected $fieldsTaken;

   function __construct() {
      $this->flags = [];
      $this->xtends = [];
      $this->imports = [];
      $this->types = [];
      $this->fields = [];
      $this->funcs = [];
      $this->provides = new Conds( $this );
      $this->stack = new Stack( $this );
   }

   abstract function className();

   abstract function isImplem();

   function name() { return $this->name; }

   function pkg() { return $this->pkg; }

   function pkgName() { return $this->pkg.".".$this->name; }

   function types() { return $this->types; }

   function funcs() { return $this->funcs; }

   function fields() { return $this->fields; }

   function ver() { return $this->ver; }

   function run( RunCtx $r ) { return $this; }

   function blockKind() { return Block::NONE; }

   function has($flag) {
      return array_key_exists( $flag, $this->flags );
   }

   /// alapértelmezett típus
   function defType() {
      if ( $this->has( self::NODEF ))
         return null;
         return $this->name;
   }

   /// típus név alapján
   function itemType( $name ) {
      return Tools::g( $this->types, $name );
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
      $this->inheritFields();
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
      if ( ! $it = $this->resolve( $m[1], ExprCtx::ITEM ))
         throw new EVy("Unknown item: $it");
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
         case ExprCtx::CONS: 
         case ExprCtx::FUNC: 
         case ExprCtx::INFIX:
            $arrs = [$this->funcs];
         break;
         case ExprCtx::NAME: 
            $arrs = [$this->fields, $this->funcs, $this->types, 
               $this->xtends, $this->imports];
         break;
         case ExprCtx::ITEM:
            $arrs = [$this->xtends, $this->imports];
         break;
         default: throw new EVy("Unknown resolve kind: $kind");
      }
      foreach ( $arrs as $a ) {
         if ( $ret = Tools::g( $a, $token )) {
            switch ($kind) {
               case ExprCtx::CONS: $ok = $ret instanceof ItemConst; break;
               case ExprCtx::INFIX:
                  $ok = $ret instanceof ItemFunc
                     && $ret->oper() && Oper::IN == $ret->oper()->kind();
               break;
               default: $ok = true;
            }
            if ($ok) return $ret;
         }
      }
      return null;
   }

   /// típusok átvétele
   protected function inheritTypes( $name, $o ) {
      foreach ( $o->types() as $t ) {
         $tn = $t->name();
         if ( $tn == $o->defType() )
            $tn = $name;
         if ( ! $tt = Tools::g( $this->types, $tn )) {
            $tt = new ItemType( $this, $tn );
            $this->add( $this->types, $tn, $tt );
         }
         $tt->add( "$name.$tn" );
      }
   }

   /// függvények átvétele
   protected function inheritFuncs() {
      if ( $this->funcsTaken ) return;
      $map = $this->typeMap();
      foreach ( $this->xtends as $x ) {
         foreach ( $x->funcs() as $f )
            $this->inheritFunc( $f, $map );
      }
      $this->funcsTaken = true;
   }

   /// mezők átvétele
   protected function inheritFields() {
      if ( $this->fieldsTaken ) return;
      $map = $this->typeMap();
      foreach ( $this->xtends as $x ) {
         foreach ( $x->fields() as $f )
            $this->inheritField( $f, $map );
      }
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
      $this->addDefType();
   }

   /// egy rész olvasása
   protected function readPart( $s ) {
      $s->readWS();
      switch ( $n = $s->next() ) {
         case self::CONST: $meth = "readConst"; break;
         case self::EXTEND: $meth = "readExtend"; break;
         case self::FIELD: $meth = "readField"; break;
         case self::FLAG: $meth = "readFlag"; break;
         case self::FUNCTION: $meth = "readFunction"; break;
         case self::IMPORT: $meth = "readImport"; break;
         case self::METHOD: $meth = "readMethod"; break;
         case self::PROVIDE:
            $meth = "readProvide";
            $this->inheritFuncs();
            $this->inheritFields();
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
         case $this->xtends: case $this->imports:
            return [$this->xtends, $this->imports];
         default:
            return [$arr];
      }
   }

   /// függvény olvasása
   protected function readConst( $s ) {
      $ret = new ItemConst( $this );
      $ret->read( $s );
      $this->add( $this->funcs, $ret->name(), $ret );
   }

   /// egy zászló olvasása
   protected function readFlag( $s ) {
      $s->readWS();
      $f = $s->readIdent();
      if ( ! in_array( $f, self::ALLFLAGS ))
         throw new EVy("Unknown flag: $f");
      $this->flags[$f] = true;
      $s->readTerm();
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
      $this->fieldsTaken = false;
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
      $ret = new ItemFunction( $this );
      $ret->read( $s );
      $this->add( $this->funcs, $ret->name(), $ret );
   }

   /// mező olvasása
   protected function readField( $s ) {
      $ret = new ItemField( $this );
      $ret->read( $s );
      $this->add( $this->fields, $ret->name(), $ret );
   }

   /// metódus olvasása
   protected function readMethod( $s ) {
      $ret = new ItemMethod( $this );
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
// Tools::debug("inheritFunc ".$f->owner()->name().".".$f->name() );      
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

   /// mező öröklése
   protected function inheritField( ItemField $f, $map ) {
      $name = $f->name();
      $fmap = $this->mapSlice( $map, $f->owner()->name() );
      if ( $g = Tools::g( $this->fields, $name ) ) {
         throw new EVy("Field already exists: $name");
      } else {
         $g = new ItemField($this);
         $g->inherit( $f, $fmap );
         $this->fields[ $name ] = $g;
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
   protected function addDefType() {
      $n = $this->name();
      $t = new ItemType( $this, $n );
      $this->add( $this->types, $n, $t );
   }

}
