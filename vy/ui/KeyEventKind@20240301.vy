interface vy.ui.KeyEventKind @20240301 {

   extend vy.core.Equal;
   
   type {
      KeyEventKind = Equal.Equal;
   }
   
   const up: KeyEventKind;
   const down: KeyEventKind;
   const press: KeyEventKind;

}
