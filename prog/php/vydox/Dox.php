<?php

namespace vy;

/// Dokumentáció készítő
class Dox 
extends Configable
{

   /// kimeneti típusok
   const
      MD = "md";

   /// blokk típusok
   const
      FILE = "file",
      CLS = "cls",
      FUNC = "func",
      CONS = "cons",
      FLD = "fld";

   /// jellemzők
   const
      INTYPE = "inType",
      LANG = "lang",
      LINKHEAD = "linkHead",
      LINKTAIL = "linkTail",
      OUTTYPE = "outType",
      STYLE = "style",
      TITLE = "title",
      WRAP = "wrap";

   const
      /// továbbított jellemzők
      PASSED = [self::LANG, self::LINKHEAD, self::LINKTAIL, self::STYLE, 
         self::TITLE, self::WRAP];
   
   /// részek
   protected $parts;
   /// olvasó
   protected $reader;
   /// író
   protected $writer;
   
   function __construct() {
      parent::__construct();
      $this->clear();
      $this->conf = [];
      $this->set( self::WRAP, 72 );
   }

   /// tartalom törlése
   function clear() {
      $this->parts = [];
   }

   /// új rész készítése
   function addPart( $depth ) {
      $ret = new DoxPart($this, $depth );
      $this->parts [] = $ret;
      return $ret;
   }
   
   /// egy forrás olvasása
   function read( $src ) {
      $this->clear();
      $t = DoxReader::guess( $src );
      $this->reader($t)->readFile( $src, $this );
   }

   /// egy kimenet írása
   function write( $dst ) {
      $this->prepare();
      $t = DoxWriter::guess( $dst );
      $ret = $this->writer($t)->write( $this );
      Tools::saveFile( $dst, $ret );
   }

   function owner() { return null; }

   function parts() { return $this->parts; }

   function typ() { return self::FILE; }

   function refs() { return []; }
   
   function rows() { return []; }
   
   function depth() { return 0; }

   function name() { return null; }

   function create() {
      $ret = new DoxPart($this);
      $this->parts [] = $ret;
      return $ret;
   }

   /// előkészülés (rendezés, stb..) írás előtt
   protected function prepare() {
      $this->prepareToc($this, $this);
   }

   /// tartalomjegyzék összeállítása
   protected function prepareToc($root, $block) {
      if ( DoxPart::TOC == $block->typ() )
         return $this->buildToc( $root, $block );
      foreach ( $block->parts() as $p )
         $this->prepareToc( $root, $p );
   }

   /// tartalomjegyzék felépítése
   protected function buildToc( $root, $toc ) {
      $toc->clear();
      foreach ( $root->parts() as $p ) {
         if ( $p->name() )
            $this->addTocItem( $toc, $p );
      }
   }

   /// az olvasó
   protected function reader( $t ) {
      if ( ! $tin = $this->get( self::INTYPE ))
         $tin = $t;
      if ( ! $this->reader || $tin != $this->reader->typ() )
         $this->reader = DoxReader::create( $tin, $this );
      return $this->reader;
   }

   /// az író
   protected function writer( $t ) {
      if ( ! $tout = $this->get( self::OUTTYPE ))
         $tout = $t;
      if ( ! $this->writer || $tout != $this->writer->typ() ) {
         $this->writer = DoxWriter::create( $tout );
      }
      foreach ( self::PASSED as $f )
         $this->writer->set( $f, $this->get($f) );
      return $this->writer;
   }
   
   /// egy toc elem hozzáadása
   protected function addTocItem( $toc, $part ) {
      $p = $toc->addPart();
      $p->setTyp( DoxPart::TOCITEM );
      $p->addRow("`[#".$part->name()."]`" );
      if ( $r = $part->rows() )
         $p->addRow( $r[0] );
   }
}
