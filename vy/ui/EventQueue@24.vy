interface vy.ui.EventQueue @24 {

   ~ nodef;

   import {
      vy.core.Bool;
      Event;
   }

   function {
      empty: Bool;
      poll: Event;
   }

}
