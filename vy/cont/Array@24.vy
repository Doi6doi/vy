interface vy.cont.Array @24 {

   extend Indexable;

   import vy.num.Uint;

   type {
      Index = Uint;
   }

   method {
      length: Index;
      setValue( Index, Value );
   }

}
