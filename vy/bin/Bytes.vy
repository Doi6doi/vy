interface vy.bin.Bytes @20240117 {

   extend {
      vy.core.Equal;
      vy.core.Indexable;
   }

   import vy.core.Uint;
   import vy.core.Uint8;

   type {
      D = Indexable.D;
      B = Equal.B;
      I = Indexable.I = Uint.U;
      Y = Indexable.A = Uint8.U;
   }

   const &hex;

   function {

      length(D): I;

   }

}

