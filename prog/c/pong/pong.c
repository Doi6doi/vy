#include <vy.h>
#include <vy_geom.h>

#include <stdbool.h>
#include <string.h>

#include "vy_string.h"
#include "vy_key.h"
#include "vy_time.h"
#include "vy_random.h"
#include "vy_rect.h"
#include "vy_font.h"
#include "vy_window.h"
#include "vy_sprite.h"
#include "vy_color.h"
#include "vy_circle.h"

#include <math.h>

#define MAXCOORD 0.7
#define TICK 0.05
#define BALLSPEED 0.05
#define BALLSIZE 0.02
#define PADWIDTH 0.08
#define PADHEIGHT 0.2
#define LEFTUP "W"
#define LEFTDOWN "S"
#define RIGHTUP "Up"
#define RIGHTDOWN "Down"

#define PONGKEY( x ) pong.keys.constUtf( strlen(x), x )

typedef enum Side { LEFT=0, RIGHT=1 } Side;

typedef VyPoint2f Point;

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
   // adatok
   float padSpeed;
   int maxScore;
   bool over;
   Stamp last;
   /// elemek
   Window window;
   Score score;
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
   pong.sprites.moveTo( pong.ball.sprite, 0, 0 );
   float r = pong.randoms.random(1)-0.5;
   pong.ball.dy = pong.ball.speed * MAXCOORD * r;
   pong.ball.dx = speedComp( pong.ball.dy ) * (RIGHT == side ? -1 : 1);
}

/// vy inicializálás
void initVy() {
   pong.vy = vyInit();
   VyContext ctx = vyContext( pong.vy );
   VYSTRINGARGS( sa );
   vyGetImplem( ctx, sa, & pong.keys );
   VyRepr s = vyGetImplemRepr( sa, "String" );
   VYKEYARGS( ka );
   vyFree( vyGetImplem( ctx, ka, & pong.keys ));
   VYTIMEARGS( ta );
   vyFree( vyGetImplem( ctx, ta, & pong.times ));
   VYRECTARGS( ra );
   vyGetImplem( ctx, ra, & pong.rects );
   VyRepr r = vyGetImplemRepr( ra, "Rect" );
   VYWINDOWARGS( wa );
   vyGetImplem( ctx, wa, & pong.windows );
   VyRepr w = vyGetImplemRepr( wa, "Window" );
/*
   vyFree( vyGetImplem( ctx, fontsArgs( d, r, s ), & pong.fonts ));

   vyFree( vyGetImplem( ctx, randomArgs(), & pong.random ));

   vyFree( vyGetImplem( ctx, spritesArgs(d), & pong.sprites ));
*/
   vyFree( sa );
   vyFree( ra );
   vyFree( wa );
}

/// pong inicializálás
void initPong() {
   pong.padSpeed = 0.01;
   pong.maxScore = 5;
   pong.over = false;
   pong.last = pong.times.stamp();
   pong.window = pong.windows.create();
   SpriteFun * sf = &pong.sprites;
   ColorFun * cf = &pong.colors;
   // score
   pong.score.text = pong.strings.constAscii(0,"");
   pong.score.sprite = sf->create();
   // ball
   Ball * b = &pong.ball;
   Circle c = pong.circles.createCircle( BALLSIZE );
   pong.circles.setColor( c, cf->white() );
   b->sprite = sf->createSprite( (Shape)c );
   b->speed = BALLSPEED;
   b->dx = b->dy = 0;
   // pads
   for (int i=LEFT; i<=RIGHT; ++i) {
      Pad * p = pong.pads+i;
      Rect r = pong.rects.createRect( 0, 0, PADWIDTH, PADHEIGHT );
      pong.rects.setColor( r, LEFT == i ? cf->blue() : cf->green() );
      p->sprite = sf->createSprite( (Shape)r );
      p->score = 0;
      if ( LEFT == i ) {
		 p->up = PONGKEY( LEFTUP );
		 p->up = PONGKEY( LEFTDOWN );
      } else {
		 p->up = PONGKEY( RIGHTUP );
		 p->down = PONGKEY( RIGHTDOWN );
      }
   }
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
   SpriteFun * sf = &pong.sprites;
   float x = sf->coord( bs, VC_CENTERX );
   sf->setCoord( bs, VC_CENTERX, x - b->dx );
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
   SpriteFun * sf = &pong.sprites;
   float bx = sf->coord( bs, VC_CENTERX );
   if ( side && bx <= 1 )
      return;
   if ( ! side && -1 <= bx )
      return;
   Pad * p = &pong.pads[side];
   float by = sf->coord( bs, VC_CENTERY );
   float py = sf->coord( p->sprite, VC_CENTERY );
   float d = py - by;
   if ( PADHEIGHT/2 < fabs( d ) )
      score( other( side ) );
      else bounce( side, d );
}


/// golyó mozgása
void moveBall( ) {
   Ball * b = &pong.ball;
   Sprite bs = b->sprite;
   SpriteFun * sf = &pong.sprites;
   float bx = sf->coord( bs, VC_CENTERX ) + b->dx;
   float by = sf->coord( bs, VC_CENTERY ) + b->dy;
   if ( by < -1 || 1 < by ) {
      b->dy = -b->dy;
      by += b->dy;
   }
   sf->setCoord( bs, VC_CENTERX, bx );
   sf->setCoord( bs, VC_CENTERY, by );
   checkHit( LEFT );
   checkHit( RIGHT );
}

/// egy ütő mozgatása
void movePad( Side side ) {
   Pad * p = pong.pads+side;
   SpriteFun * sf = &pong.sprites;
   float py = sf->coord( p->sprite, VC_CENTERY );
   if ( pong.keys.pressed( p->up ) ) {
      py -= pong.padSpeed;
      if ( py < -1 ) py = -1;
   }
   if ( pong.keys.pressed( p->down ) ) {
      py += pong.padSpeed;
      if ( 1 < py ) py = 1;
   }
   sf->setCoord( p->sprite, VC_CENTERY, py );
}

/// kép frissítése
void tick() {
   TimeFun * t = &pong.times;
   Stamp next = t->addSecond( pong.last, TICK );
   t->waitUntil( next );
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
   vyFree( pong.vy );
}

int main() {
   init();
   while ( ! pong.over )
      step();
   done();
   return 0;
}
