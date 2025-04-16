class vyi.game.pong.Ball @24 {

   import {
      vy.ui.Sprite;
      Number = vy.num.Math;
   }

   const {
      X: Coord = x;
      Y: Coord = y;
   }

   type {
      Number = Vec.Base;
   }   

   field {
      sprite: Sprite;
      speed: Number;
      dx, dy: Number;
   }

   method {
      speedComp( c:Number ): Number {
         return ( speed*speed - c*c ).sqrt;
      }

      move {
         x := sprite.coord( X ) + dx;
         y := sprite.coord( Y ) + dy;
         if ( y < -1 || 1 < y ) {
            dy := -dy;
            y += dy;
         }
         sprite.setCoord( X, x );
         sprite.setCoord( Y, y );
      }
   }

}
