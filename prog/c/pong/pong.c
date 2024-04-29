#include <vy.h>
#include <vy_geom.h>

#include <stdbool.h>
#include <string.h>

#include "vy_string.h"
#include "vy_key.h"
#include "vy_time.h"
#include "vy_random.h"
#include "vy_rect.h"
#include "vy_window.h"
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
#define BALLCOLOR "fff"
#define PADWIDTH 0.08
#define PADHEIGHT 0.2
#define LEFTUP "W"
#define LEFTDOWN "S"
#define LEFTCOLOR "0f0"
#define RIGHTUP "Up"
#define RIGHTDOWN "Down"
#define RIGHTCOLOR "00f"

#define PONGKEY( x ) keys.constUtf( x, VY_COUNT )

Vy vy;
// implementációk
StringFun strings;
KeyFun keys;
TimeFun times;
RandomFun randoms;
WindowFun windows;
RectFun rects;
SpriteFun sprites;
ColorFun colors;
CircleFun circles;
CaptionFun captions;
FilledFun filleds;

typedef enum Side { LEFT=0, RIGHT=1 } Side;

/// pontszám szöveg
typedef struct Score {
   String text;
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
   Key up;
   Key down;
} Pad;

/// minden pong adat
typedef struct Pong {
   // adatok
   float padSpeed;
   int maxScore;
   bool over;
   Stamp last;
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
   float r = randoms.random(1)-0.5;
   pong.ball.dy = pong.ball.speed * MAXCOORD * r;
   pong.ball.dx = speedComp( pong.ball.dy ) * (RIGHT == side ? -1 : 1);
}

/// vy inicializálás
void initVy() {
   vy = vyInit();
   VyContext ctx = vyContext( vy );
   VYSTRINGARGS( sa );
   vyGetImplem( ctx, sa, & strings );
   VyRepr s = vyGetRepr( sa, "String" );
   VYKEYARGS( ka );
   vyFree( vyGetImplem( ctx, ka, & keys ));
   VYTIMEARGS( ta );
   vyFree( vyGetImplem( ctx, ta, & times ));
   VYRECTARGS( ra );
   vyGetImplem( ctx, ra, & rects );
   VyRepr r = vyGetRepr( ra, "Rect" );
   VYWINDOWARGS( wa );
   vyGetImplem( ctx, wa, & windows );
   VyRepr w = vyGetRepr( wa, "Window" );
/*
   vyFree( vyGetImplem( ctx, randomArgs(), & pong.random ));

   vyFree( vyGetImplem( ctx, spritesArgs(d), & pong.sprites ));
*/
   vyFree( sa );
   vyFree( ra );
   vyFree( wa );
}

/// pontszám kijelzés inicializálás
void initScore( Side side ) {
   Score * s = pong.scores + side;
   s->text = strings.constAscii("", VY_COUNT);
   Caption c = captions.createCaption( s->text );
   s->sprite = sprites.createSprite( (Shape)c);
   windows.add( pong.window, s->sprite );
}

/// labda inicializálás
void initBall() {
   Ball * b = &pong.ball;
   b->speed = BALLSPEED;
   b->dx = b->dy = 0;
   Circle c = circles.createCircle( BALLSIZE );
   VyColor o = colors.constHex( BALLCOLOR, VY_COUNT );
   Filled fc = filleds.createFilled( (Shape)c, o );
   b->sprite = sprites.createSprite( (Shape)fc );
   windows.add( pong.window, b->sprite );
}

void initPad( Side side ) {
   Pad * p = pong.pads + side;
   Rect r = rects.createRect( 0, 0, PADWIDTH, PADHEIGHT );
   VyColor c = colors.constHex( LEFT == side ? LEFTCOLOR : RIGHTCOLOR, VY_COUNT );
   Filled fc = filleds.createFilled( (Shape)r, c );
   p->sprite = sprites.createSprite( (Shape)fc );
   p->score = 0;
   if ( LEFT == side ) {
      p->up = PONGKEY( LEFTUP );
      p->up = PONGKEY( LEFTDOWN );
   } else {
      p->up = PONGKEY( RIGHTUP );
      p->down = PONGKEY( RIGHTDOWN );
   }
   windows.add( pong.window, p->sprite );
}

/// pong inicializálás
void initPong() {
   pong.padSpeed = 0.01;
   pong.maxScore = 5;
   pong.over = false;
   pong.last = times.stamp();
   pong.window = windows.create();
   initScore( LEFT );
   initScore( RIGHT );
   initPad( LEFT );
   initPad( RIGHT );
   initBall();
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
   Stamp next = times.addSecond( pong.last, TICK );
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
   vyFree( vy );
}

int main() {
   init();
   while ( ! pong.over )
      step();
   done();
   return 0;
}
