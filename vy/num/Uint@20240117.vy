interface vy.num.Uint @20240117 {

   extend {
      Number;
      vy.core.Compare;
      vy.core.Ordinal;
   }

   type Uint = Compare.Compare = Number.Num;

   const &dec;

   provide {
      given a:U;
      0 <= a;
   }

}

