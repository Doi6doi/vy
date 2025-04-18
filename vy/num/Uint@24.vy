interface vy.num.Uint @24 {

   extend {
      Number;
      vy.core.Compare;
      vy.core.Ordinal;
   }

   provide {
      given( a:Uint ) {
         0 <= a;
      }
   }

}

