interface vy.bin.Bytes @24 {

   extend {
      vy.core.Equal;
      vy.core.Indexable;
   }

   import {
      vy.core.Uint;
      vy.core.Uint8;
   }

   type {
      Index = Uint;
      Uint8 = Indexable.Value;
   }

   const &hex;

   method {
      length: Uint;
   }

}

