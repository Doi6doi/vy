<?php

namespace vy;

/// szöveges kiíró
class OStream {

   /// fájl neve
   protected $filename;
   /// kimeneti nyél
   protected $fh;
   /// indentálás
   protected $indent;

   function __construct( $filename ) {
      $this->indent = 0;
      $this->filename = $filename;
      if ( ! $this->fh = fopen( $filename, "w" ))
         throw new EVy("Cannot open ".$filename." for writing");
   }

   function filename() { return $this->filename; }

   /// idnentálás
   function indent( $inc ) {
      if ($inc)
         ++$this->indent;
      else if ( 0 < $this->indent )
         --$this->indent;
   }

   /// kiírás
   function write( $str ) {
      fwrite( $this->fh, $str );
   }

   /// kiírás helyettesítéssel sorvégével
   function writel() {
      if ( $a = func_get_args() ) {
         $this->writeIndent();
         $this->write( call_user_func_array("sprintf", $a ));
      }
      $this->write("\n");
   }

   /// kiírás formázva
   function writef() {
      $this->write( call_user_func_array("sprintf", func_get_args() ));
   }

   /// indentálás kiírása
   function writeIndent() {
      $this->write( str_repeat("   ",$this->indent));
   }

   function __destruct() {
      $this->close();
   }

   /// fájl bezárása
   function close() {
      if ( $this->fh ) {
         fclose( $this->fh );
         $this->fh = null;
      }
   }

}
