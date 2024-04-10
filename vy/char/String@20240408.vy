interface vy.char.String @20240408 {

   extend {
      vy.core.Compare;
      vy.cont.Array;
   }

   type {
      Char = Array.Base;
      String = Compare.C;
   }

   const {
      &ascii: String;
      &utf: String;   
   }
   
}
