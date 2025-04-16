interface vy.ui.EventQueue @24 {

   import {
      vy.core.Bool;
      Event;
   }

   type &nodef;

   function {
      empty: Bool;
      poll: Event;
   }

}
