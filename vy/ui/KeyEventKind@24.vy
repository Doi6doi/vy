interface vy.ui.KeyEventKind @24 {

   extend vy.core.Equal;
   
   type {
      KeyEventKind = Equal.Equal;
   }
   
   const up: KeyEventKind;
   const down: KeyEventKind;
   const press: KeyEventKind;

}
