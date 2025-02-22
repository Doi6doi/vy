interface vy.ui.EventQueue @24 {

   import {
      vy.core.Bool;
      Event;
   }

   function {
      empty: Bool;
      poll: Event;
   }

}
