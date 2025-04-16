class vyi.game.pong.Pong @25 {

   extend vy.core.Run;

   import {
      Pad;
      vy.num.Uint;
      vy.num.Math;
      vy.geom2.Dir;
      vy.cont.Indexable;
      vy.ui.SpriteUI;
   }

   type {
      Number = Math;
   }

   const {

      BALLSPEED = 0.05;
      BALLSIZE = 0.02;
      BALLCOLOR: Color = "\xff\xff\xff";

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
      LEFTCOLOR: Color = "\x00\xff\x00";

      RIGHTUP: Key = "up";
      RIGHTDOWN: Key = "down";
      RIGHTCOLOR: Color = "\x00\x00\xff";

      TICK = 0.05;

      ESCAPE: Key = "esc"; 
   }

   field {
      over: Bool = false;
      maxScore: Uint = MAXSCORE;
      last: Time;
      window: Window;
      scores: Indexable { Index = Dir; Value = Uint; }
      ball: Ball;
      pads: Indexable { Index = Dir; Value = Pad; }
   }

   function run {
      p := Pong();
      while ( ! p.over )
         p.step();
   }

   method {

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
         if ( KeyEventKind.down = e.keyKind && ESC = e.key )
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
         bx := ball.sprite.coord( Coord.x );
         if ( Dir.right = side && bx <= 1 ) return;
         if ( Dir.left = side && -1 <= bx ) return;
         p &= pads[side];
         by := ball.sprite.coord( Coord.y );
         py := p.sprite.coord( Coord.y );
         d := py - by;
         if ( PADHEIGHT/2 < d.abs )
            score( side.opposite );
            else bounce( side, d );
      }
   }
}


/// új játék
void newRound( Side side ) {
   sprites.moveTo( pong.ball.sprite, 0, 0 );
   float r = (randoms.random(100)-50)/50;
   pong.ball.dy = pong.ball.speed * MAXCOORD * r;
   pong.ball.dx = speedComp( pong.ball.dy ) * (RIGHT == side ? -1 : 1);
}

/// oldalhoz tartozó szín
VyColor sideColor( Side side ) {
   return colors.constHex( LEFT == side ? LEFTCOLOR : RIGHTCOLOR, 3 );
}

/// pontszám kijelzés inicializálás
void initScore( Side side ) {
   Score * s = pong.scores + side;
   String r = strings.constAscii("", VY_LEN);
   Caption c = captions.createCaption( r, fonts.constDefault() );
   Filled f = filleds.createFilled( (Shape)c, sideColor(side));
   Transformed td = transformeds.createTransformed( (Shape)f );
   Transform t = transformeds.transform(td);
   transforms.scale( t, SCORESIZE, SCORESIZE );
   s->sprite = sprites.createSprite( (Shape)td );
   windows.add( pong.window, (View)s->sprite );
}

/// labda inicializálás
void initBall() {
   Ball * b = &pong.ball;
   b->speed = BALLSPEED;
   b->dx = b->dy = 0;
   Circle c = circles.constCircle();
   VyColor o = colors.constHex( BALLCOLOR, 3 );
   Filled fc = filleds.createFilled( (Shape)c, o );
   Transformed td = transformeds.createTransformed( (Shape)fc );
   Transform t = transformeds.transform( td );
   transforms.scale( t, BALLSIZE, BALLSIZE );
   b->sprite = sprites.createSprite( (Shape)td );
   windows.add( pong.window, (View)b->sprite );
}

void initPad( Side side ) {
   Pad * p = pong.pads + side;
   Square s = squares.constSquare();
   Filled fc = filleds.createFilled( (Shape)s, sideColor( side ) );
   Transformed td = transformeds.createTransformed( (Shape)fc );
   Transform t = transformeds.transform( td );
   transforms.scale( t, PADWIDTH, PADHEIGHT );
   p->sprite = sprites.createSprite( (Shape)td );
   p->score = 0;
   if ( LEFT == side ) {
      p->up = PONGKEY( LEFTUP );
      p->up = PONGKEY( LEFTDOWN );
   } else {
      p->up = PONGKEY( RIGHTUP );
      p->down = PONGKEY( RIGHTDOWN );
   }
   windows.add( pong.window, (View)p->sprite );
}

/// pong inicializálás
void initPong() {
   pong.padSpeed = 0.01;
   pong.maxScore = 5;
   pong.over = false;
   pong.last = times.stamp();
   pong.window = windows.createWindow();
   pong.esc = PONGKEY(ESCAPE);
   initScore( LEFT );
   initScore( RIGHT );
   initPad( LEFT );
   initPad( RIGHT );
   initBall();
}

/// pong felszámolás
void donePong() {
   vySet( (VyAny *)&pong.window, NULL );
}


/// inicializálás
void init() {
   initVy();
   initPong();
}

/// pontszerzés
void score( Side side ) {
   if ( pong.maxScore <= ++ pong.pads[side].score )
      pong.over = true;
      else newRound( side );
}

/// másik oldal
Side other( Side s ) {
   return LEFT == s ? RIGHT: LEFT;
}

/// visszapattanás
void bounce( Side side, float d ) {
   Ball * b = &pong.ball;
   Sprite bs = b->sprite;
   float x = sprites.coord( bs, VC_CENTERX );
   sprites.setCoord( bs, VC_CENTERX, x - b->dx );
   b->dy = d;
   float s = fabs(b->dy) / b->speed;
   if ( MAXCOORD < s )
      b->dy *= (MAXCOORD/s);
   b->dx = speedComp( b->dy );
   if ( RIGHT == side )
      b->dx = - b->dx;
}







