#include "vy.h"
#include <stdbool.h>
#include <stdlib.h>
#include <math.h>

#define MAXCOORD 0.7
#define TICK 0.05

typedef float Coord;
typedef enum Side { LEFT=0, RIGHT=1 } Side;

typedef VyType Key;
typedef VyType Stamp;
typedef VyType Sprite;
typedef VyType String;
typedef VyType Scene;
typedef VyType Canvas;
typedef VyType Display;

/// billentyűzet
typedef struct Keys {
   Key (* byConst)( VyCStr cons );
   bool (* pressed)( Key key );
} Keys;

/// string
typedef struct Strings {
   String (* create)();
} Strings;

/// véletlen
typedef struct Random {
   float (* random)( float limit );
} Random;

/// idő
typedef struct Time {
   Stamp (* stamp)();
   Stamp (* addSecond)( Stamp, float );
   bool (*waitUntil)( Stamp );
} Time;

/// felület
typedef struct Displays {
   Display (* create)();
} Displays;

/// spriteok
typedef struct Sprites {
   Scene (* createScene)();
   void (* destroyScene)( Scene );
   Sprite (* createSprite)( Scene );
   void (* destroySprite)( Sprite );
   void (* callbacks)( Sprite s, VyFunc1 onBounds, VyFunc1 onDraw );
   void (* move)( Sprite s, float x, float y );
   void (* draw)( Scene s, Display d );
} Sprites;

/// pontszám szöveg
typedef struct Score {
   String text;
   Sprite sprite;
} Score;

/// a golyó adatai
typedef struct Ball {
   Coord speed;
   Coord x, y;
   Coord dx, dy;
   Sprite sprite;
} Ball;

/// egy ütő adatai
typedef struct Pad {
   Coord y;
   Coord size;
   int score;
   Key up;
   Key down;
   Sprite sprite;
} Pad;

/// minden pong adat
typedef struct Pong {
   Vy vy;
   // implementációk
   Strings strings;
   Keys keys;
   Time time;
   Random random;
   Sprites sprites;
   Displays displays;
   // adatok
   Coord padSpeed;
   int maxScore;
   bool over;
   Stamp last;
   /// elemek
   Scene scene;
   Display display;
   Score score;
   Ball ball;
   Pad pads[2];
} Pong;

Pong pong;

/// sebesség másik komponense
Coord speedComp( Coord c ) {
   return sqrt( pong.ball.speed * pong.ball.speed - c*c );
}

/// új játék
void newRound( Side side ) {
   float r = pong.random.random(1)-0.5;
   pong.ball.x = pong.ball.y = 0;
   pong.ball.dy = pong.ball.speed * MAXCOORD * r;
   pong.ball.dx = speedComp( pong.ball.dy ) * (RIGHT == side ? -1 : 1);
}

/// vy inicializálás
void initVy() {
   Vy vy = pong.vy = vyCreate();
   VyRepr rBool = vyNative(vy,VN_BOOL);
   VyRepr rFloat = vyNative(vy,VN_FLOAT);
   VyRepr rFunc = vyNative(vy,VN_FUNC);
   VyVer ver = vyVer(20240327);
   VyCStr sts [] = {"S"};
   VyRepr srs [] = {NULL};
   VyCStr sfs [] = {"create"};
   VyImplemArgs sia = { .name="vy.string.String", .ver=ver, .ntypes=1, .types=sts,
      .reprs=srs, .nfuncs=1, .funcs=sfs };
   vyGetImplem( vy, &sia, &pong.strings );
   VyCStr kts [] = {"K","B"};
   VyRepr krs [] = {NULL,rBool};
   VyCStr kfs [] = {"byName","pressed"};
   VyImplemArgs kia = { .name="vy.ui.Keys", .ver=ver, .ntypes=2, .types=kts,
      .reprs=krs, .nfuncs=2, .funcs=kfs };
   vyGetImplem( vy, &kia, &pong.keys );
   VyCStr tts [] = {"S","N","B"};
   VyRepr trs [] = {NULL,rFloat,rBool};
   VyCStr tfs [] = {"stamp","addSecond","waitUntil"};
   VyImplemArgs tia = { .name="vy.time.Time", .ver=ver, .ntypes=3, .types=tts,
      .reprs=trs, .nfuncs=3, .funcs=tfs };
   vyGetImplem( vy, &tia, &pong.time );
   VyCStr rts [] = {"N"};
   VyRepr rrs [] = {rFloat};
   VyCStr rfs [] = {"random"};
   VyImplemArgs ria = { .name="vy.random.Random", .ver=ver, .ntypes=1, .types=rts,
      .reprs=rrs, .nfuncs=1, .funcs=rfs };
   vyGetImplem( vy, &ria, &pong.random );
   VyCStr dts [] = {"D"};
   VyRepr drs [] = {NULL};
   VyCStr dfs [] = {"create","destroy"};
   VyImplemArgs dia = { .name="vy.ui.CanvasDisplay", .ver=ver, .ntypes=1,
      .types=dts, .reprs=drs, .nfuncs=2, .funcs=dfs };
   vyGetImplem( vy, &dia, &pong.displays );
   VyCStr nts [] = {"C","S","F","N","V"};
   VyRepr nrs [] = {NULL,NULL,rFunc,rFloat,dia.reprs[0]};
   VyCStr nfs [] = {"createScene","destroyScene","createSprite",
         "destroySprite","callbacks","move","draw"};
   VyImplemArgs nia = { .name="vy.ui.Sprite", .ver=ver, .ntypes =5,
      .types=nts, .reprs=nrs, .nfuncs=7, .funcs=nfs };
   vyGetImplem( vy, &nia, &pong.sprites );
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
   pong.score.text = pong.strings.create();
   pong.score.sprite = ss->createSprite( pong.scene );
   ss->callbacks( pong.score.sprite, scoreBounds, scoreDraw );
   Ball * b = &pong.ball;
   b->speed = 0.02;
   b->x = b->y = b->dx = b->dy = 0;
   b->sprite = ss->createSprite( pong.scene );
   ss->callbacks( b->sprite, ballBounds, ballDraw );
   for (int i=LEFT; s<=RIGHT; ++s) {
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
void bounce( Side side, Coord d ) {
   Ball * b = &pong.ball;
   b->x -= b->dx;
   b->dy += d;
   Coord s = fabs(b->dy) / b->speed;
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
   Coord d = p->y - b->y;
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
   s->draw( update( pong.scene );
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
   vyDestroy( pong.vy );
}

int main() {
   init();
   while ( ! pong.over )
      step();
   done();
   return 0;
}
