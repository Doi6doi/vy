<?php

namespace vy;

class MakeImport
   extends ExprCtxForward
   implements Expr, ExprCtx
{
	
	protected $names;
	
	static function load( $owner, $name ) {
	   switch ( $name ) {
	      case MakeArc::ARC: return new MakeArc( $owner );
          case MakeC::C: return new MakeC( $owner );
          case MakeCpp::CPP: return new MakeCpp( $owner );
	      case MakeComp::COMP: return new MakeComp( $owner );
          case MakeDeb::DEB: return new MakeDeb( $owner );
          case MakeDox::DOX: return new MakeDox( $owner );
          case MakeGit::GIT: return new MakeGit( $owner );
          case MakeNet::NET: return new MakeNet( $owner );
          case MakeDebug::DEBUG: return new MakeDebug( $owner );
	 	  default: throw new EVy("Unknown import: $name");
	   }
	}
	
	protected $name;

	protected function __construct( $owner, $name ) {
	   parent::__construct( $owner );
	   $this->name = $name;
	   $this->names = [];
	}
	
    function run( RunCtx $ctx ) { return $this; }
	
    function start() { }	

    function names() { return $this->names; }
	
	function __toString() { return $this->name; }
	
	function member( $field ) {
	   if ( ! array_key_exists( $field, $this->names ))
	      throw new EVy(sprintf("Unknown member: %s.%s",
	         $this->name, $field ));
	   return $this->names[$field];
	}
	
	protected function add( $name, $val ) {
	   if ( array_key_exists( $name, $this->names ))
	      throw new EVy("Duplicate name: $name");
	   $this->names[$name] = $val;
	}
	
   /// függvények hozzáadása
   protected function addFuncs( $arr ) {
	  foreach ( $arr as $a )
	     $this->addFunc( $a );
   }
	
   /// egy függvény hozzáadása
   function addFunc( $name ) {
      $f = new MakeFunc( $this );
      $f->setCall( $name, [$this,$name] );
	   $this->add( $name, $f );
	   return $f;
   }
	
   /// naplózás	
   protected function log( $lvl, $msg ) {
	  $this->owner->log( $lvl, $msg );
   }
}
