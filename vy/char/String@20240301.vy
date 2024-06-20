interface vy.char.String @20240301 {

   extend vy.core.Compare;

   import vy.num.Uint;

   type {
      Char;
      String = Compare.Compare;
      Index = Uint.Uint;
   }

   const {
      &ascii: String;
      &utf: String;   
   }

   function {
      length( String ): Index;
      charAt( Index ): Char;
   }
   
}
