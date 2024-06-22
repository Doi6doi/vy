#include <vy_implem.h>
#include <vy_font.h>
#include <vy_string.h>

#include "vysdl.h"
#include <SDL2/SDL_ttf.h>

#define NODEFFONT "Cannot open default font"
#define NOINIT "Cannot initialize SDL fonts"
#define NOSETSIZE "Could not set font size"
#define NOSIZE "Could not get text size"


#define DEFSIZE 100

#define BUFSIZE 2048
static char buf[BUFSIZE];

void vySdlFontError( VyCStr msg ) {
   snprintf( buf, BUFSIZE, "%s: %s", msg, TTF_GetError() );
   vyThrow( buf );
}

struct Font {
   struct VyRefCount ref;
   TTF_Font * ttf;
   unsigned ttfSize;
};

VyRepr vyrFont;

Font vySdlDefaultFont = NULL;

void vySdlDestroyFont( VyPtr ) {
   vyThrow("stub vySdlDestroyFont");
}

static Font vySdlFontConstDefault() {
   if ( ! vySdlDefaultFont ) {
      Font ret = vyAlloc( vyrFont );
      vyRefInit( (VyRefCount)ret );
      SDL_RWops * rw = SDL_RWFromMem( dvs_mini_data, dvs_mini_len );
      ret->ttfSize = DEFSIZE;
      if ( ! ( ret->ttf = TTF_OpenFontRW( rw, true, DEFSIZE ) ))
         vySdlFontError( NODEFFONT );
      vySdlDefaultFont = ret;
   }
   return vySdlDefaultFont;
}
 
static void vySdlSetFontSize( Font f, int size ) {
   if ( f->ttfSize == size )
      return;
   if ( 0 != TTF_SetFontSize( f->ttf, size ))
      vySdlFontError( NOSETSIZE );
   f->ttfSize = size;
}

extern float vySdlFontHeight( Font f ) {
   vySdlSetFontSize( f, DEFSIZE );
   return TTF_FontHeight( f->ttf );
}

   
float vySdlFontWidth( Font f, String s ) {
   vySdlSetFontSize( f, DEFSIZE );
   int w, h;
   if ( 0 != TTF_SizeUNICODE( f->ttf, (Uint16 *)vyStringPtr( s ), &w, &h ))
      vySdlFontError( NOSIZE );
   return (float)w / DEFSIZE;
}


void vySdlInitFont( VyContext ctx ) {
   if ( 0 != TTF_Init() )
      vySdlFontError( NOINIT );
   VYFONTARGS( ctx, args );
   vyrFont = vyRepr( "Font", sizeof(struct Font), vySetRef, vySdlDestroyFont);
   vyArgsType( args, "Font", vyrFont );
   vyArgsImpl( args, "constDefault", vySdlFontConstDefault );
   vyAddImplem( ctx, args );
}

