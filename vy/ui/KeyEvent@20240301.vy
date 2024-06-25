interface vy.ui.KeyEvent @20240301 {

   import {
      Key;
      KeyEventKind;
   }

   extend Event;

   type {
      KeyEvent = Event.Event;
   }
   
   function {
      keyKind( KeyEvent ): KeyEventKind;
      key( KeyEvent ): Key;
   }


}
