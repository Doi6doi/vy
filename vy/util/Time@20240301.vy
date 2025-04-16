interface vy.time.Time @24 {

   extend vy.core.Assign;

   import {
      vy.num.Number;
   }

   function {
      current(): Time;
      Time.addSecond( Number ): Time;
      waitUntil( Time );
   }

}
