interface vy.ui.EventQueue @20240301 {

   import {
      vy.core.Bool;
      Event;
   }

   function {
      empty: Bool;
      poll: Event;
   }

}
