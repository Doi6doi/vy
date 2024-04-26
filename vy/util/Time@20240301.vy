interface vy.time.Time @20240301 {

   import {
      vy.num.Number;
      vy.core.Bool;
   }

   type {
      Stamp;
   }

   function {
      stamp(): Stamp;
      addSecond( Stamp, Number ): Stamp;
      waitUntil( Stamp ): Bool;
   }

}
