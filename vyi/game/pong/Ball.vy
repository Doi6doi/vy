class vyi.game.pong.Ball @25 {

   extend {
      vy.ui.Sprite;
   }

   import {
      Pong;
      Number = vy.num.Math;
   }

   const {
      X = Coord.x;
      Y = Coord.y;
   }

   field {
      speed: Number;
      dx: Number;
      dy: Number;
   }

   method {

      Ball( pong: Pong ) {
         super( Filled( $circle, pong.BALLCOLOR ));
         speed( pong.BALLSPEED );
         transform.scale( pong.BALLSIZE, pong.BALLSIZE );
      }

      speedComp( c:Number ): Number {
         return (speed*speed - c*c).sqrt;
      }

      move {
         x := coord( X ) + dx;
         y := coord( Y ) + dy;
         if ( y < -1 || 1 < y ) {
            dy := -dy;
            y += dy;
         }
         coord(X) := x;
         coord(Y) := y;
      }

      /// visszapattanás
      bounce( Dir side, Number d ) {
         x := sprite.coord( X );
         sprite.setCoord( X, x - dx );
         dy := d;
         s := dy.abs / speed;
         if ( MAXCOORD < s )
            dy *= (MAXCOORD/s);
         dx := speedComp( dy );
         if ( Dir.right = side )
            dx := -dx;
      }

      /// újra megjelenés
      appear( side: Dir ) {
         coord(X) := coord(Y) := 0;
         r := (random(100)-50)/50;
         dy := speed * MAXCOORD * r;
         dx = speedComp( dy ) * (Dir.right = side ? -1 : 1);
      }

   }

}
