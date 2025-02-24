<?php

namespace vy;

class MakeGit extends MakeImportCmd {
   
   const
      GIT = "Git";

   protected $git;
   
   function __construct( $owner ) {
	   parent::__construct( $owner, self::GIT );
      $this->setGit( null );
 	   $this->addFuncs( ["clone"] );
   }

   function cmd() { return $this->git; }

   /// git beállítása
   function setGit( $git ) {
	  $this->git = Git::create( $git );
   }

   /// repo klónozása
   function clone( $url ) {
      $this->git->clone( $url );
   }
   
}
