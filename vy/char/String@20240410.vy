interface vy.char.String @20240410 {

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
