interface vy.geom.Rect @20240301 {

   extend Shape;

   type {
      Rect=Shape.Shape;
   }
   
   function {
      createRect( left:Coord, top: Coord, width:Coord, height:Coord ): Rect;
   }

}
