<?php

namespace vy;

class MakeGit extends MakeImport {
   
   const
      GIT = "Git";

   protected $git;
   
   function __construct( $owner ) {
	   parent::__construct( $owner, self::GIT );
      $this->setGit( null );
 	   $this->addFuncs( ["clone"] );
   }

   /// git beállítása
   function setGit( $git ) {
	  $this->git = Git::create( $git );
   }

   /// repo klónozása
   function clone( $url ) {
      $this->git->clone( $url );
   }
   
   
   
   
}
