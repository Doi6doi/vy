#include <vy.h>
#include <vy_geom.h>
#include <vy_util.h>
#include <vy_ui.h>

#include <stdbool.h>
#include <string.h>
#include <stdio.h>
#include <vy_implem.h>

#include "vy_string.h"
#include "vy_key.h"
#include "vy_time.h"
#include "vy_random.h"
#include "vy_square.h"
#include "vy_transform.h"
#include "vy_transformed.h"
#include "vy_window.h"
#include "vy_font.h"
#include "vy_sprite.h"
#include "vy_color.h"
#include "vy_circle.h"
#include "vy_caption.h"
#include "vy_filled.h"

#include <math.h>

#define MAXCOORD 0.7
#define TICK 0.05
#define BALLSPEED 0.05
#define BALLSIZE 0.02
#define BALLCOLOR "\xff\xff\xff"
#define PADWIDTH 0.08
#define PADHEIGHT 0.2
#define SCORETOP 0.05
#define SCORESIDE 0.1
#define SCORESIZE 0.2
#define LEFTUP "W"
#define LEFTDOWN "S"
#define LEFTCOLOR "\x00\xff\x00"
#define RIGHTUP "Up"
#define RIGHTDOWN "Down"
#define RIGHTCOLOR "\x00\x00\xff"

#define PONGKEY( x ) keys.constUtf( x, VY_LEN )

Vy vy;
// implementációk
StringFun strings;
KeyFun keys;
TimeFun times;
RandomFun randoms;
FontFun fonts;
WindowFun windows;
SquareFun squares;
SpriteFun sprites;
ColorFun colors;
CircleFun circles;
CaptionFun captions;
FilledFun filleds;
TransformFun transforms;
TransformedFun transformeds;

typedef enum Side { LEFT=0, RIGHT=1 } Side;

typedef struct Score {
   Sprite sprite;
} Score;

/// a golyó adatai
typedef struct Ball {
   Sprite sprite;
   float speed;
   float dx;
   float dy;
} Ball;

/// egy ütő adatai
typedef struct Pad {
   Sprite sprite;
   int score;
   VyKey up;
   VyKey down;
} Pad;

/// minden pong adat
typedef struct Pong {
   // adatok
   float padSpeed;
   int maxScore;
   bool over;
   VyStamp last;
   /// elemek
   Window window;
   Score scores[2];
   Ball ball;
   Pad pads[2];
} Pong;

Pong pong;

/// sebesség másik komponense
float speedComp( float c ) {
   return sqrt( pong.ball.speed * pong.ball.speed - c*c );
}

/// új játék
void newRound( Side side ) {
   sprites.moveTo( pong.ball.sprite, 0, 0 );
   float r = (randoms.random(100)-50)/50;
   pong.ball.dy = pong.ball.speed * MAXCOORD * r;
   pong.ball.dx = speedComp( pong.ball.dy ) * (RIGHT == side ? -1 : 1);
}

/// vy inicializálás
void initVy() {
   vy = vyInit();
   VyContext ctx = vyContext( vy );
   VYIMPORTSTRING( ctx, strings );
   VYIMPORTFONT( ctx, fonts );
   VYIMPORTKEY( ctx, keys );
   VYIMPORTTIME( ctx, times );
   VYIMPORTRANDOM( ctx, randoms );
   VYIMPORTSQUARE( ctx, squares );
   VYIMPORTWINDOW( ctx, windows );
   VYIMPORTSPRITE( ctx, sprites );
   VYIMPORTCOLOR( ctx, colors );
   VYIMPORTCIRCLE( ctx, circles );
   VYIMPORTCAPTION( ctx, captions );
   VYIMPORTFILLED( ctx, filleds );
   VYIMPORTTRANSFORM( ctx, transforms );
   VYIMPORTTRANSFORMED( ctx, transformeds );
   
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
   VyColor c = sideColor( side );
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

/// visszapattanás vagy új kör
void checkHit( Side side ) {
   Ball * b = &pong.ball;
   Sprite bs = b->sprite;
   float bx = sprites.coord( bs, VC_CENTERX );
   if ( side && bx <= 1 )
      return;
   if ( ! side && -1 <= bx )
      return;
   Pad * p = &pong.pads[side];
   float by = sprites.coord( bs, VC_CENTERY );
   float py = sprites.coord( p->sprite, VC_CENTERY );
   float d = py - by;
   if ( PADHEIGHT/2 < fabs( d ) )
      score( other( side ) );
      else bounce( side, d );
}


/// golyó mozgása
void moveBall( ) {
   Ball * b = &pong.ball;
   Sprite bs = b->sprite;
   float bx = sprites.coord( bs, VC_CENTERX ) + b->dx;
   float by = sprites.coord( bs, VC_CENTERY ) + b->dy;
   if ( by < -1 || 1 < by ) {
      b->dy = -b->dy;
      by += b->dy;
   }
   sprites.setCoord( bs, VC_CENTERX, bx );
   sprites.setCoord( bs, VC_CENTERY, by );
   checkHit( LEFT );
   checkHit( RIGHT );
}

/// egy ütő mozgatása
void movePad( Side side ) {
   Pad * p = pong.pads+side;
   float py = sprites.coord( p->sprite, VC_CENTERY );
   if ( keys.pressed( p->up ) ) {
      py -= pong.padSpeed;
      if ( py < -1 ) py = -1;
   }
   if ( keys.pressed( p->down ) ) {
      py += pong.padSpeed;
      if ( 1 < py ) py = 1;
   }
   sprites.setCoord( p->sprite, VC_CENTERY, py );
}

/// kép frissítése
void tick() {
   VyStamp next = times.addSecond( pong.last, TICK );
   times.waitUntil( next );
   pong.last = next;
}

/// lépés
void step() {
   movePad( LEFT );
   movePad( RIGHT );
   moveBall();
   tick();
}


/// vége
void done() {
   donePong();
   vyFree( vy );
}

int main() {
   init();
   while ( ! pong.over )
      step();
   done();
   return 0;
}
