interface vy.cont.Dict @20240117 {

   import {
      vy.str.String;
      vy.core.Any;
   }

   type {
      D;
      S = String.S;
      A = Any.A;
   }

   const []:D;

   function {
      has(D,S):B
      get( D, S ): A { indexget; }
   }

   provide {
      given s:S;
      ! has([],s)
   }

}
