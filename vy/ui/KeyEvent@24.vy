interface vy.ui.KeyEvent @24 {

   extend Event;

   import {
      Key;
      KeyEventKind;
   }

   method {
      kind: KeyEventKind;
      key: Key;
   }


}
