interface vy.geom2.Vec @24 {

   extend vy.num.Number;

   import {
      Base = vy.num.Number;
      Coord;
   }

   method {
      coord( Coord ): & Base { oper [] };
   }

   const {
      &0: Vec;
   }

}
