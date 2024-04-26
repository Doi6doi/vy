#include <vy.h>
#include <vy_geom.h>

#include <stdbool.h>

#include "vy_string.h"
#include "vy_key.h"
#include "vy_time.h"
#include "vy_random.h"
#include "vy_rect.h"
#include "vy_font.h"
#include "vy_window.h"
#include "vy_sprite.h"

#include <math.h>

#define MAXCOORD 0.7
#define TICK 0.05

typedef enum Side { LEFT=0, RIGHT=1 } Side;

typedef VyPoint2f Point;

/// pontszám szöveg
typedef struct Score {
   String text;
   Sprite sprite;
} Score;

/// a golyó adatai
typedef struct Ball {
   float speed;
   Sprite sprite;
   VyPoint2f delta;
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
   float r = pong.randoms->random(1)-0.5;
   Point p = {.x=0, .y=0};
   pong.sprites->place( pong.ball.sprite, p );
   float dy pong.ball.delta.y = pong.ball.speed * MAXCOORD * r;
   pong.ball.delta.x = speedComp( dy ) * (RIGHT == side ? -1 : 1);
}

/// vy inicializálás
void initVy() {
   pong.vy = vyInit();
   VyContext ctx = vyContext( pong.vy );

   VyImplemArgs sa = vyGetImplem( ctx, stringsArgs(), & pong.strings );
   VyRepr s = vyGetImplemRepr( sa, "String" );

   vyFree( vyGetImplem( ctx, keysArgs(), & pong.keys ));

   vyFree( vyGetImplem( ctx, timeArgs(), & pong.time ));

   VyImplemArgs ra = vyGetImplem( ctx, rectsArgs(), & pong.rects );
   VyRepr r = vyGetImplemRepr( ra, "Rect" );

   VyImplemArgs da = vyGetImplem( ctx, displaysArgs(), & pong.displays );
   VyRepr d = vyGetImplemRepr( da, "Display" );

   vyFree( vyGetImplem( ctx, fontsArgs( d, r, s ), & pong.fonts ));

   vyFree( vyGetImplem( ctx, randomArgs(), & pong.random ));

   vyFree( vyGetImplem( ctx, spritesArgs(d), & pong.sprites ));

   vyFree( sa );
   vyFree( ra );
   vyFree( da );
}

/// pong inicializálás
void initPong() {
   pong.padSpeed = 0.01;
   pong.maxScore = 5;
   pong.over = false;
   pong.last = pong.time.stamp();
   pong.display = pong.displays.create();
   Sprites * ss = &pong.sprites;
   pong.scene = ss->createScene();
   pong.score.text = pong.strings.constAscii(0,"");
   pong.score.sprite = ss->createSprite( pong.scene );
   ss->callbacks( pong.score.sprite, scoreBounds, scoreDraw );
   Ball * b = &pong.ball;
   b->speed = 0.02;
   b->x = b->y = b->dx = b->dy = 0;
   b->sprite = ss->createSprite( pong.scene );
   ss->callbacks( b->sprite, ballBounds, ballDraw );
   for (int i=LEFT; i<=RIGHT; ++i) {
      Pad * p = pong.pads+i;
      p->y = 0;
      p->size = 0.1;
      p->score = 0;
      p->up = pong.keys.byConst( LEFT ? "W" : "Up" );
      p->down = pong.keys.byConst( RIGHT ? "S" : "Down" );
      p->sprite = ss->createSprite( pong.scene );
      ss->callbacks( p->sprite, padBounds, padDraw );
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
   b->x -= b->dx;
   b->dy += d;
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
   if ( side && b->x <= 1 )
      return;
   if ( side && -1 <= b->x )
      return;
   Pad * p = &pong.pads[side];
   float d = p->y - b->y;
   if ( p->size < fabs( d ) )
      score( other( side ) );
      else bounce( side, d );
}


/// golyó mozgása
void moveBall( ) {
   Ball * b = &pong.ball;
   b->x += b->dx;
   b->y += b->dy;
   if ( b->y < -1 || 1 < b->y ) {
      b->dy = -b->dy;
      b->y += b->dy;
   }
   checkHit( LEFT );
   checkHit( RIGHT );
}

/// egy ütő mozgatása
void movePad( Side side ) {
   Pad * p = pong.pads+side;
   if ( pong.keys.pressed( p->up ) ) {
      p->y -= pong.padSpeed;
      if ( p->y < -1 ) p->y = -1;
   }
   if ( pong.keys.pressed( p->down ) ) {
      p->y += pong.padSpeed;
      if ( 1 < p->y ) p->y = 1;
   }
}

/// újrarajzolás
void redraw() {
   Sprites * s = &pong.sprites;
   Ball * b = &pong.ball;
   s->move( b->sprite, b->x, b->y );
   Pad * p = pong.pads+LEFT;
   s->move( p->sprite, -1, p->y );
   Pad * q = pong.pads+RIGHT;
   s->move( q->sprite, 1, q->y );
   pong.sprites.draw( pong.scene, pong.display );
}


/// kép frissítése
void tick() {
   Time * t = &pong.time;
   Stamp next = t->addSecond( pong.last, TICK );
   if ( t->waitUntil( next ) )
      redraw();
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
