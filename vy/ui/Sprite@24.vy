interface vy.ui.Sprite @24 {

   import vy.geom.Shape;

   extend vy.ui.View;

   type Sprite=View.View;

   function {
      createSprite( Shape ): Sprite;
      moveTo( Sprite, x:Coord, y:Coord );
      setShape( Sprite, Shape );
   }   

}
