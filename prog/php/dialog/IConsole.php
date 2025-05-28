<?php

namespace vy;

/// console-kezelő
interface IConsole {

   /// inicializálás
   function init();
   
   /// lezárás
   function done();

   /// képernyő törlése
   function clear();

   /// képernyő mérete
   function size();

   /// háttérszín beállítása
   function setBack(Color $x);
   
   /// előtérszín beállítása
   function setFore(Color $x);

   /// cím beállítása
   function setTitle($x);

   /// vágási terület
   function crop($r);

   /// kitöltött téglalap
   function fillRect(Rect $r);

   /// esemény olvasása
   function pollEvent();

   /// rajzolás frissítése
   function refresh();

   /// szöveg kiírása
   function text( Point $at, $txt );

}

