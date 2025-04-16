interface vy.char.String @24 {

   extend {
      vy.core.Compare;
      vy.core.Assign;
   }

   import vy.num.Uint;

   type {
      Char;
      Index = Uint;
   }

   const {
      &ascii: String;
      &utf: String;   
   }

   method {
      length: Index;
      charAt( Index ): Char;
   }
   
}
