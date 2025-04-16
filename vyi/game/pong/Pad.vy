class Pad @25 {

   import {
      vy.num.Uint;
      Number = vy.num.Math;
      vy.geom2.Dir;
      vy.ui.SpriteUI;
   }

   const {
      KEYUP : KeyEventKind = up;
      KEYDOWN : KeyEventKind = down;

      CY : Coord = centery;
   }

   field {
      sprite: Sprite = create;
      speed: Number;
      score: Uint;
      dir: Dir;
      up: Key;
      down: Key;
   }
      
   method {
      
      /// billentyű esemény hattatás egy oldalra
      handleKey( e: KeyEvent ) {
         case (e.key) {
            up: case (e.kind) {
               KEYUP: dir := Dir.up;
               KEYDOWN: if ( Dir.up = dir )
                  dir := Dir.none;
            }
            down: case (e.kind) {
               KEYUP: dir := Dir.down;
               KEYDOWN:if ( Dir.down = dir ) 
                  dir := Dir.none;
            }
         }
      }

      /// egy ütő mozgatása
      move {
         y := sprite.coord( CY );
         case (dir) {
            up: y -= speed;
            down: y += speed;
         }
         sprite.setCoord( CY, y.clamp(-1,1) );
      }
   }
}

