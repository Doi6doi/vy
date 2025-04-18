interface vy.geom2.Vec @24 {

   extend vy.num.Number;

   import {
      Base = vy.num.Number;
      Coord;
   }

   const {
      zero;
   }

   method {
      coord( Coord ) & Base { oper [] }
   }

}
