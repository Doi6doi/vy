interface vy.cont.Vector @20240301 {

   extend Array;

   type Vector = Array.Array;

   function {
      createVector: Vector;
      insert( Vector, Index, Value );
      remove( Vector, Index );
   }

}
