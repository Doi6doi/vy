interface vy.num.Uint @20240117 {

   extend {
      Number;
      vy.core.Compare;
      vy.core.Ordinal;
   }

   type Uint = Compare.Compare = Number.Number;

   const &dec;

   provide {
      given( a:Uint ) {
         0 <= a;
      }
   }

}

