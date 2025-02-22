<?php

namespace vy;

/// dox-ban egy rész
class DoxPart {

   /// rész típusok
   const
      CLS = "cls",
      FIELD = "field",
      FUNC = "func",
      MACRO = "macro",
      METHOD = "method",
      PARAM = "param",
      RECORD = "record",
      RETURN = "return",
      SCOPE = "scope",
      TOC = "toc",
      TOCITEM = "tocItem",
      TYPE = "type";
      
   /// escape parancsok
   const
      REF = "ref";
   
   /// tartalmazó 
   protected $owner;
   /// típus
   protected $typ;
   /// sorok
   protected $rows;
   /// további részek
   protected $parts;
   /// hivatkozott rész
   protected $refs;
   /// elem mélysége
   protected $depth;
   /// elem neve
   protected $name;
      
   function __construct( $owner, $depth ) {
      $this->owner = $owner;
      $this->clear();
      $this->depth = $depth;
   }
   
   function owner() { return $this->owner; }
   
   function rows() { return $this->rows; }
   
   function parts() { return $this->parts; }
   
   function typ() { return $this->typ; }   
   
   function refs() { return $this->refs; }
   
   function depth() { return $this->depth; }
   
   function name() { return $this->name; }
   
   /// ref sor hozzáadása
   function addRef($x) { 
      $this->refs [] = $x; 
   }
   
   /// sor hozzáadása
   function addRow( $r ) {
      $this->rows [] = $r;
   }

   /// típus megadása
   function setTyp($x) { 
      $this->typ = $x; 
   }

   /// név megadása
   function setName($x) {
      if ( ! $this->name )
         $this->name = $x;
   }

   /// tartalom törlése
   function clear() {
      $this->refs = [];
      $this->rows = [];
      $this->parts = [];
   }

   /// új alrész
   function addPart( $depth=null ) {
      $ret = new DoxPart($this, $depth );
      $this->parts [] = $ret;
      return $ret;
   }
   
   
   
}
