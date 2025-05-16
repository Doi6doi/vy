<?php

namespace vy;

class Text
   extends View
{

   const
      ALIGN = "align",
      FONT = "font",
      ROWCOUNT = "rowCount",
      ROWHEIGHT = "rowHeight",
      TEXT = "text";

   protected $rowHeight;
   protected $rows;
   protected $align;
   protected $font;

   function __construct($txt="") {
      parent::__construct();
      $this->align = Coord::LEFT;
      $this->rows = [];
      $this->font = Font::byOwner($this);
      $this->setText($txt);
   }

   function preferred() {
      $ret = null;
      $g = VypGui::gui();
      $f = $this->font;
      foreach ( $this->rows as $r ) {
         $s = $g->textSize($r,$f);
         if ( ! $ret ) {
            $ret = $s;
         } else {
            $ret->x = max( $ret->x, $s->x );
            $ret->y += $this->rowHeight();
         }
      }
      return $ret;
   }

   function __get($fld) {
      switch ($fld) {
         case self::ALIGN: return $this->align;
         case self::FONT: return $this->font;
         case self::ROWCOUNT: return $this->rowCount();
         case self::ROWHEIGHT: return $this->rowHeight();
         case self::TEXT: return implode("\n",$this->rows);
         default: return parent::__get($fld);
      }
   }

   function __set($fld,$x) {
      switch ($fld) {
         case self::TEXT: return $this->setText($x);
         default: parent::__set($fld,$x);
      }
   }

   function build( $d ) {
      Tools::buildProps( $this, $d,
         [self::ALIGN, self::ROWHEIGHT, self::TEXT]);
      $this->font->build( Tools::g( $d, self::FONT ));
   }

   function paint( Canvas $c ) {
      $y = 0;
      for ($i=0; $i<count($this->rows); ++$i) {
         $this->paintRow($c,$i,$y);
         $y += $this->rowHeight();
      }
   }

   protected function setParent($x) {
      parent::setParent($x);
      if ($x && false !== $this->font->proto)
         $this->font->proto = $x->font;
   }

   /// egy sor kirajzolása
   protected function drawRow($i) {
      return VypGui::gui()->drawText( $this->rows[$i], $this->font );
   }

   /// sortávolság
   protected function rowHeight() {
      if ( $this->rowHeight )
         return $this->rowHeight;
         else return $this->font->size*1.2;
   }

   /// sorok száma
   protected function rowCount() {
      return count($this->rows);
   }

   /// új szöveg beállítása
   protected function setText($x) {
      $arr = explode("\n",$x);
      if ( $arr === $this->rows )
         return;
      $this->rows = $arr;
      if ( $this->parent )
         $this->parent->layout(true);
   }

   /// egy sor kirajzolása
   protected function paintRow( Canvas $c, $i, $y ) {
      static $p = new Point();
      $g = VypGui::gui();
      $txt = $this->rows[$i];
      $ts = $g->textSize( $txt, $this->font );
      $p->y = $y;
      switch ( $this->align ) {
         case Coord::LEFT: $p->x = 0; break;
         case Coord::RIGHT: $p->x = $this->bounds->width-$ts->x; break;
         case Coord::XCENTER: $p->x = ($this->bounds->width-$ts->x)/2; break;
         default: throw Coord::unknown( $this->align );
      }
      $this->skin->paintText( $c, $this, $p, $txt );
   }

}
