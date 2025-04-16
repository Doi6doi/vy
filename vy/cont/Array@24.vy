interface vy.cont.Array @24 {

   extend Indexable;

   import vy.num.Uint;

   type {
      Index = Uint;
   }

   const {
      &list( Value );
   }

   method {
      length: Index;
   }

}
