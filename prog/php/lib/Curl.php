<?php

namespace vy;

/// curl letöltő
class Curl
   extends Configable
{
	
   const
      DEST = "dest",
      CERT = "cert",
      PROGRESS = "progress",
      URL = "url";

   function __construct() {
	  parent::__construct();
	  $this->set(self::CERT,true);
   }

   function fetch( $args ) {
	  if ( ! $args ) return;
	  if ( ! is_array( $args ))
	     $args = [self::URL=>$args];
	  $args = array_merge( $this->conf, $args );
	  $c = curl_init();
	  curl_setopt( $c, CURLOPT_FOLLOWLOCATION, true );
	  if ( ! $url = Tools::g( $args, self::URL ))
	     throw new EVy("url missing");
	  curl_setopt( $c, CURLOPT_URL, $url );
	  if ( $dst = Tools::g( $args, self::DEST )) {
		 if ( ! $fh = fopen( $dst, "w" ))
		    throw new EVy("Cannot write: $dst");
	     curl_setopt( $c, CURLOPT_FILE, $fh );
	  } else {
		 curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
      }
      if ( $prg = Tools::g( $args, self::PROGRESS )) {
         curl_setopt( $c, CURLOPT_NOPROGRESS, false );
         if ( is_callable( $prg ))
            curl_setopt( $c, CURLOPT_PROGRESSFUNCTION, $prg );
      }
      curl_setopt( $c, CURLOPT_USERAGENT, "curl" );
      if ( ! Tools::g( $args, self::CERT )) {
		 curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, false );
		 curl_setopt( $c, CURLOPT_SSL_VERIFYHOST, false );
	  }
      $ret = curl_exec( $c );
      if ( $e = curl_error( $c ) )
         throw new EVy("Curl error: $e");
      curl_close( $c );
      if ( $dst )
         fclose( $fh );
         else return $ret;
   }	    

   protected function confKind( $fld ) {
	  switch ( $fld ) {
		 case self::CERT: 
		 case self::URL: 
		    return Configable::SCALAR;
		 case self::PROGRESS: return Configable::ANY;
         default: return Configable::NONE;
      }
   }

}
