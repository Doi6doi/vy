interface vy.geom.Rect @20240301 {

   extend Shape;

   type {
      Rect=Shape.Shape;
   }
   
   function {
      createRect( width:Coord, height:Coord ): Rect;
      width( Rect ): Coord;
      height( Rect ): Coord;
   }

}
