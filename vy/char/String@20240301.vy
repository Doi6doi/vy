interface vy.char.String @24 {

   extend {
      vy.cont.Array;
      vy.core.Compare;
      vy.core.Assign;
   }

   import vy.num.Uint;

   type {
      Char = Array.Value;
   }

   const {
      &ascii: String;
      &utf: String;
   }

   method {
      length: Index;
      charAt( Index ) & Char;
   }

}
