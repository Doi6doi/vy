class Pad @25 {

   extend {
      vy.ui.Sprite;
   }

   import {
      Pong;
      vy.num.Uint;
      Number = vy.num.Math;
      vy.geom2.Dir;
      vy.geom2.Square;
   }

   const {
      KEYUP : KeyEventKind = up;
      KEYDOWN : KeyEventKind = down;

      Y : Coord = y;
   }

   field {
      pong & Pong;
      side: Dir;
      dir: Dir;
   }
      
   method {

      Pad( pong:Pong, side:Dir ) {
         super( Filled( Square.square, pong.sideColor(side) ));
         pong(pong);
         side(side);
         transform.scale( pong.PADWIDTH, pong.PADHEIGHT );
         transform.move( pong.MAXCOORD * pong.dx(side), 0 );
      }
      
      /// billentyű esemény hattatás egy oldalra
      handleKey( e: KeyEvent ) {
         case (e.key) {
            key(Dir.up): case (e.kind) {
               KEYUP: dir := Dir.up;
               KEYDOWN: if ( Dir.up = dir )
                  dir := Dir.none;
            }
            key(Dir.down): case (e.kind) {
               KEYUP: dir := Dir.down;
               KEYDOWN:if ( Dir.down = dir ) 
                  dir := Dir.none;
            }
         }
      }

      /// orányhoz tartozó gomb
      key(d:Dir): Key { page.sideKey(d) }

      /// egy ütő mozgatása
      move {
         y := coord( Y );
         case (dir) {
            up: y -= speed;
            down: y += speed;
         }
         coord(Y) := y.clamp(-1,1);
      }
   }
}

