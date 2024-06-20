#include <vy_implem.h>
#include <vy_font.h>

#include "vysdl.h"
#include <SDL2/SDL_ttf.h>

#define NODEFFONT "Cannot open default font"
#define NOINIT "Cannot initialize SDL fonts"
#define BUFSIZE 2048
static char buf[BUFSIZE];

void vySdlFontError( VyCStr msg ) {
   snprintf( buf, BUFSIZE, "%s: %s", msg, TTF_GetError() );
   vyThrow( buf );
}

struct Font {
   struct VyRefCount ref;
   TTF_Font * ttf;
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
      if ( ! ( ret->ttf = TTF_OpenFontRW( rw, true, 12 ) ))
         vySdlFontError( NODEFFONT );
      vySdlDefaultFont = ret;
   }
   return vySdlDefaultFont;
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

