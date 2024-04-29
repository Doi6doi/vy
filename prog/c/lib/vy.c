#include "vy.h"
#include "vy_implem.h"
#include "vy_string.h"

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#define REALLOC(p,s) realloc( p, s)
#define VYALLOC(t,r) (t *)vyAlloc( r )

#define BUFSIZE 512

char
   *VYNOMEM = "Out of memory";

char
   *NOCONTEXT = "Missing context",
   *NOIMPARGS = "Missing implementations arguments",
   *NOIMPDEST = "Missing implementation destination",
   *NOINTF = "Missing interface name",
   *NONATIVE = "Missing native name",
   *NOTYPE = "Missing type",
   *NOVER = "Missing version",
   *UNIMP = "Unknown implementation: %s@%d",
   *UNNATIVE = "Unknown native type: %s",
   *UNREPR = "Unknown representation: %s";

char vyBuf[BUFSIZE];

struct VyRepr {
   size_t size;
   bool stat;
   VyDestr destr;
};

struct VyArgs {
   VyRepr repr;
   VyCStr intf;
   VyVer ver;
   unsigned ntype;
   VyCStr * types;
   VyRepr * reprs;
   unsigned nfunc;
   VyCStr * funcs;
   VyPtr * impls;
};

struct VyContext {
   VyRepr repr;
   Vy vy;
   int nimpls;
   VyArgs * impls;
};

struct Vy {
   VyRepr repr;
   VyContext context;
};

void vyDestroyContext( VyContext ctx ) {
   ctx->impls = REALLOC( ctx->impls, 0 );
}

void vyDestroyVy( Vy vy ) {
   vyFree( vy->context );
}

struct VyRepr vyrVy = {
   .size = sizeof(struct Vy),
   .stat = false,
   .destr = (VyDestr)vyDestroyVy
};

struct VyRepr vyrContext = {
   .size = sizeof(struct VyContext),
   .stat = false,
   .destr = (VyDestr)vyDestroyContext
};

struct VyRepr vyrWchar = {
   .size = sizeof( wchar_t ),
   .stat = true,
   .destr = NULL
};

struct VyRepr vyrUnsigned = {
   .size = sizeof( unsigned ),
   .stat = true,
   .destr = NULL
};

struct VyRepr vyrSize = {
   .size = sizeof( size_t ),
   .stat = true,
   .destr = NULL
};

struct VyRepr vyrBool = {
   .size = sizeof( bool ),
   .stat = true,
   .destr = NULL
};

bool vySameStr( VyCStr a, VyCStr b ) {
   if ( !a || !b ) return false;
   return 0 == strcmp( a, b );
}

void vyDestroyArgs( VyArgs ia ) {
   ia->types = REALLOC( ia->types, 0 );
   ia->reprs = REALLOC( ia->reprs, 0 );
   ia->funcs = REALLOC( ia->funcs, 0 );
   ia->impls = REALLOC( ia->impls, 0 );
}

struct VyRepr vyrVyArgs = {
   .size = sizeof(struct VyArgs),
   .stat = false,
   .destr = (VyDestr)vyDestroyArgs
};

void vyThrow( VyCStr msg ) {
   fprintf( stderr, "%s\n", msg );
   exit(1);
}

VyPtr vyAlloc( VyRepr r ) {
   VyPtr ret = REALLOC( NULL, r->size );
   *((VyRepr *)ret) = r;
   return ret;
}

VyVer vyVer( unsigned u ) {
   return u;
}

void vyFree( void * obj ) {
   VyRepr r = *(VyRepr *)obj;
   if ( r->destr )
      r->destr( obj );
   obj = REALLOC( obj, 0 );
}

VyContext vyContextCreate( Vy vy ) {
   VyContext ret = VYALLOC( struct VyContext, & vyrContext );
   ret->vy = vy;
}

Vy vyInit() {
   Vy ret = VYALLOC( struct Vy, & vyrVy );
   if ( ! ret ) vyThrow( VYNOMEM );
   ret->context = vyContextCreate( ret );
   vyInitString( ret->context );
   return ret;
}

VyContext vyContext( Vy vy ) {
   return vy->context;
}

VyRepr vyNative( VyCStr name ) {
   if ( ! name )
      vyThrow( NONATIVE );
   switch ( *name ) {
      case 'b':
         if ( vySameStr("bool",name))
            return & vyrBool;
      break;
      case 's':
         if (vySameStr("size",name))
            return & vyrSize;
      break;
      case 'u':
         if (vySameStr("uint", name) || vySameStr("unsigned", name))
            return & vyrUnsigned;
      break;
      case 'w':
         if (vySameStr("wchar_t", name))
            return & vyrWchar;
      break;
   }
   snprintf( vyBuf, BUFSIZE, UNNATIVE, name );
   vyThrow( vyBuf );
}

/// függvény keresés név alapján
static int vyFindFunc( VyArgs ia, VyCStr func ) {
   for (int i=0; i < ia->nfunc; ++i) {
      if ( vySameStr( ia->funcs[i], func ))
         return i;
   }
   return -1;
}

/// egy függvény implementációja
VyPtr vyGetFunc( VyArgs args, VyCStr name ) {
   int i = vyFindFunc( args, name );
   if ( 0 <= i )
      return args->impls[i];
      else return NULL;
}

/// illeszkednek-e az implementációk
bool vyMatchesImpl( VyArgs args, VyArgs other ) {
   if ( args->ver != other->ver ) return false;
   if ( ! vySameStr( args->intf, other->intf )) return false;
   if ( args->ntype != other->ntype ) return false;
   for ( int i=0; i<args->ntype; ++i) {
      VyRepr r = args->reprs[i];
      VyRepr ro = vyGetRepr( other, args->types[i] );
      if ( ! ro ) return false;
      if ( NULL != r && r != ro ) return false;
   }
   for ( int i=0; i<args->nfunc; ++i) {
      VyPtr f = vyGetFunc( other, args->funcs[i] );
      if ( ! f )
         return false;
   }
   return true;
}

/// implementáció hattatása
void vyApplyImpl( VyArgs args, VyArgs dest, VyPtr * ptrs ) {
   for (unsigned i=0; i < dest->ntype; ++i) {
      if ( dest->reprs[i] )
         dest->reprs[i] = vyGetRepr( args, dest->types[i] );
   }
   for (unsigned i=0; i < dest->nfunc; ++i)
      ptrs[i] = vyGetFunc( args, dest->funcs[i] );
}


/// implementáció lekérése
VyArgs vyGetImplem( VyContext ctx, VyArgs args, void * dest ) {
   if ( ! ctx ) vyThrow( NOCONTEXT );
   if ( ! args ) vyThrow( NOIMPARGS );
   if ( ! dest ) vyThrow( NOIMPDEST );
   for ( unsigned i=0; i<ctx->nimpls; ++i) {
      VyArgs ia = ctx->impls[i];
      if ( vyMatchesImpl( args, ia ) ) {
         vyApplyImpl( ia, args, (VyPtr *)dest );
         return args;
      }
   }
   snprintf( vyBuf, BUFSIZE, UNIMP, args->intf, args->ver );
   vyThrow( vyBuf );
}

VyRepr vyRepr( size_t size, bool stat, VyDestr destr ) {
   VyRepr ret = REALLOC( NULL, sizeof( struct VyRepr ) );
   ret->size = size;
   ret->stat = stat;
   ret->destr = destr;
   return ret;
}

/// típus keresés név alapján
static int vyFindType( VyArgs ia, VyCStr type ) {
   for (int i=0; i < ia->ntype; ++i) {
      if ( vySameStr( ia->types[i], type ))
         return i;
   }
   return -1;
}

VyRepr vyGetRepr( VyArgs ia, VyCStr type ) {
   if ( ! ia ) vyThrow( NOIMPARGS );
   if ( ! type ) vyThrow( NOTYPE );
   int i = vyFindType( ia, type );
   if ( 0 <= i )
      return ia->reprs[i];
   snprintf( vyBuf, BUFSIZE, UNREPR, type );
   vyThrow( vyBuf );
}

VyArgs vyArgs( VyCStr intf, VyVer ver ) {
   if ( ! intf ) vyThrow( NOINTF );
   if ( ! ver ) vyThrow( NOVER );
   VyArgs ret = VYALLOC( struct VyArgs, & vyrVyArgs );
   if ( ! ret ) vyThrow( VYNOMEM );
   ret->intf = intf;
   ret->ver = ver;
   ret->ntype = 0;
   ret->types = NULL;
   ret->reprs = NULL;
   ret->nfunc = 0;
   ret->funcs = NULL;
   ret->impls = NULL;
   return ret;
}


void vyArgsType( VyArgs ia, VyCStr type, VyRepr repr ) {
   int i = vyFindType( ia, type );
   if ( 0 <= i ) {
	  ia->reprs[i] = repr;
	  return;
   }
   unsigned n = ia->ntype+1;
   VyCStr * types = REALLOC( ia->types, n*sizeof(VyPtr) );
   if ( ! types ) vyThrow( VYNOMEM );
   VyRepr * reprs = REALLOC( ia->reprs, n*sizeof(VyPtr) );
   if ( ! reprs ) vyThrow( VYNOMEM );
   types[n-1] = type;
   reprs[n-1] = repr;
   ia->types = types;
   ia->reprs = reprs;
   ia->ntype = n;
}

void vyArgsFunc( VyArgs ia, VyCStr func ) {
   vyArgsImpl( ia, func, NULL );
}

void vyArgsImpl( VyArgs ia, VyCStr func, VyPtr impl ) {
   int i = vyFindFunc( ia, func );
   if ( 0 <= i ) {
	  ia->impls[i] = impl;
	  return;
   } 
   unsigned n = ia->nfunc+1;
   VyCStr * funcs = REALLOC( ia->funcs, n*sizeof(VyPtr) );
   if ( ! funcs ) vyThrow( VYNOMEM );
   VyPtr * impls = REALLOC( ia->impls, n*sizeof(VyPtr) );
   if ( ! impls ) vyThrow( VYNOMEM );
   funcs[n-1] = func;
   impls[n-1] = impl;
   ia->funcs = funcs;
   ia->impls = impls;
   ia->nfunc = n;
}

void vyAddImplem( VyContext ctx, VyArgs ia ) {
   unsigned n = ctx->nimpls+1;
   VyArgs * impls = REALLOC( ctx->impls, n*sizeof(VyPtr) );
   if ( ! impls ) vyThrow( VYNOMEM );
   impls[n-1] = ia;
   ctx->impls = impls;
   ctx->nimpls = n;
}
