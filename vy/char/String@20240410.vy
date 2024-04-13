interface vy.char.String @20240410 {

   extend {
      vy.core.Compare;
      vy.cont.Array;
   }

   type {
      Char = Array.Base;
      String = Compare.Compare;
   }

   const {
      &ascii: String;
      &utf: String;   
   }
   
}
