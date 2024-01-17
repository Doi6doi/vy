interface vy.num.Uint @20240117 {

   extends {
      Number;
      vy.core.Compare;
      vy.core.Ordinal;
   }

   type U = Compare.C = Number.N;

   const &dec;

   provide {
      given a:U;
      0 <= a;
   }

}

