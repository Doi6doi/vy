interface vy.num.Uint @24 {

   extend {
      Number;
      vy.core.Compare;
      vy.core.Ordinal;
   }

   const &dec: Uint;

   provide {
      given( a:Uint ) {
         0 <= a;
      }
   }

}

