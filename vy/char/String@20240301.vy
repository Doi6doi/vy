interface vy.char.String @20240301 {

   extend {
      vy.core.Compare;
   }

   type {
      Char;
      String = Compare.Compare;
   }

   const {
      &ascii: String;
      &utf: String;   
   }
   
}
