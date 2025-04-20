class vyi.game.pong.Pong @25 {

   extend vy.core.Run;

   import {
      Pad;
      Ball;
      Score;
      vy.cont.Indexable;
   }

   const {
      BGCOLOR: Color = #000000;

      BALLSPEED = 0.05;
      BALLSIZE = 0.02;
      BALLCOLOR: Color = #ffffff;

      PADWIDTH = 0.08;
      PADHEIGHT = 0.2;
      PADSPEED = 0.3;

      SCORETOP = 0.05;
      SCORESIDE = 0.1;
      SCORESIZE = 0.2;

      MAXSCORE = 3;
      MAXCOORD = 0.7;

      LEFTUP: Key = "w";
      LEFTDOWN: Key = "s";
      LEFTCOLOR: Color = #00ff00;

      RIGHTUP: Key = "up";
      RIGHTDOWN: Key = "down";
      RIGHTCOLOR: Color = #0000ff;

      TICK = 0.05;

      ESCAPE: Key = "esc"; 

      X = Coord.x;
      Y = Coord.y;

      L = Dir.left;
      R = Dir.right;
   }

   field {
      over: Bool;
      last: Time;
      window: Window;
      scores: Indexable { Index = Dir; Value = Score }
      pads: Indexable { Index = Dir; Value = Pad }
      ball: Ball;
   }

   function run {
      p: Pong;
      while ( ! p.over )
         p.step();
   }

   method {

      Pong {
         window.add( ball(this) );
         window.add( pads[L] := Pad( this, L ) );
         window.add( pads[R] := Pad( this, R ) );
         window.add( scores[L] := Score( this, L ) );
         window.add( scores[R] := Score( this, R ) );
         last( Time.now );
      }

      /// a játék egy lépése
      step {
         handleEvents;
         pads[left].move;
         pads[right].move;
         moveBall;
         tick;
      }

      /// esemény feldolgozás
      handleEvents {
         while ( ! $empty ) {
            e := $poll;
            if ( key = e.kind )
               handleKey( e );
         }
      }

      /// billentyű esemény feldolgozás
      handleKey( e:KeyEvent ) {
         if ( KeyEventKind.down = e.keyKind && ESCAPE = e.key )
            over := true;
         pads[left].handleKey(e);
         pads[right].handleKey(e);
      }

      /// kép frissítése
      tick {
         next := last.addSecond( TICK );
         $waitUntil( next );
         last := next;
      }

      /// golyó mozgása
      moveBall {
         ball.move;
         checkHit( Dir.left );
         checkHit( Dir.right );
      }

      /// visszapattanás vagy új kör
      checkHit( Dir side ) {
         bx := ball.sprite.coord( X );
         if ( Dir.right = side && bx <= 1 ) return;
         if ( Dir.left = side && -1 <= bx ) return;
         p &= pads[side];
         by := ball.sprite.coord( Y );
         py := p.sprite.coord( Y );
         d := py - by;
         if ( PADHEIGHT/2 < d.abs )
            score( side.opposite );
            else ball.bounce( side, d );
      }

      /// pontszerzés
      score( side:Side ) {
         s := scores[side] + 1;
         scores[side] := s;
         if ( maxScore <= s )
            over = true;
            else newRound( side );
      }

      /// oldalhoz tartozó szín
      sideColor( side:Side ) : Color {
         case (side) {
             Dir.left : LEFTCOLOR;
             Dir.right: RIGHTCOLOR;
             else BGCOLOR;
         }
      }

      /// új játék
      newRound( side: Side ) {
         ball.appear( side );
      }
   }

}















