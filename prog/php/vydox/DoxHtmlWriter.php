<?php

namespace vy;

// html dox író
class DoxHtmlWriter extends DoxWriter {

   function typ() { return DoxWriter::HTML; }
   
   function write( $b ) {
      switch ( $b->typ() ) {
         case Dox::FILE: return $this->writeFile( $b );
         default: return parent::write( $b );
      }
   }

   /// escape-elés
   function esc( $x ) {
      return htmlspecialchars( $x );
   }

   function format( $r ) {
      $r = $this->esc( $r );
      return parent::format( $r );
   }

   function formatPart( $part, $m ) {
      switch ( $part ) {
         case self::BR: return $this->br(true);
         case self::HEAD;
            $n = strlen( $m[1] );
            return "<h$n>$m[2]</h$n>";
         break;
         case self::STRONG: return "<strong>$m[1]</strong>";
         case self::EM: return "<em>$m[1]</em>";
         case self::CODE: return "<code>$m[1]</code>";
         case self::REFS: 
            if ( $this->fld())
               return "<code>$m[0]</code>";
               else return "<pre><code>$m[0]</code></pre>\n";
         case self::LST: return '<div class="li">'.$m[1].'</div>';
         default: return parent::formatPart( $part, $m );
      }
   }

   protected function formatLink( $txt, $lnk ) {
      return '<a href="'.$lnk.'">'.$txt.'</a>';
   }

   protected function writePart( $refs, $rows, $parts ) {
      $tt = null;
      if ( $t = $this->block->typ() ) $tt = " $t";
      $nn = null;
      if ( ($n = $this->block->name())
         && ! in_array( $t, [DoxPart::FIELD, DoxPart::PARAM] ))
         $nn = ' id="'.$n.'"';
      return '<div class="part'.$tt.'"'.$nn.'>'."\n"
         .parent::writePart( $refs, $rows, $parts )."\n</div>\n";
   }

   /// sortörés elem
   protected function br($large=false) {
      return $large
         ? '<div class="lbr"></div>'."\n"
         : '<div class="br"></div>';
   }

   /// vonal elem
   protected function hr() {
      return '<div class="hr"></div>';
   }

   /// ez egy mezős blokk
   protected function flc() {
      return in_array( $this->block->typ(),
         [DoxPart::RECORD, DoxPart::FUNC, DoxPart::TOC] );
   }
   
   /// ez egy mező blokk
   protected function fld() {
      return in_array( $this->block->typ(),
         [DoxPart::FIELD, DoxPart::PARAM, DoxPart::RETURN, DoxPart::TOCITEM] );
   }
         
   protected function sep($kind) {
      switch ($kind) {
         case self::SROW: case self::SREF: return "\n";
         case self::SREFS: case self::SROWS:
            return $this->fld() ? " " : $this->br();
         case self::SPART:
            return $this->flc() ? $this->br() : $this->hr();
         default: return parent::sep($kind);
      }
   }
         
   protected function writeRefs( array $fs ) {
      if ( DoxPart::RETURN == $this->block->typ() )
         return parent::writeRefs(["return"]);
      return parent::writeRefs( $fs );
   }
         
   /// teljes html fájl kiírása
   function writeFile( $b ) {
      if ( $l = $this->get( Dox::LANG ))
         $l = ' lang="'.$this->esc($l).'"';
      if ( $t = $this->get( Dox::TITLE ))
         $t = '<title>'.$this->esc($t).'</title>';
      if ( $s = $this->get( Dox::STYLE ))
         $s = '<link rel="stylesheet" href="'.$s.'">';
      $ret = [
         '<!DOCTYPE html>',
         '<html'.$l.'>',
         '<head>',
         '<meta charset="UTF-8">',
         '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
         $t,
         $s,
         '</head>',
         '<body>'
      ];
      $ret [] = parent::write( $b );
      $ret [] = '</body></html>';
      return implode("\n",$ret);
   }
}
