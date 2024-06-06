#include "vy.h"
#include "vy_implem.h"
#include "vy_sm.h"

#include "vy_core.h"
#include "vy_util.h"
#include "vy_geom.h"
#include "vy_ui.h"
#include "vy_arch.h"

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#define REALLOC(p,s) realloc( p, s)
#define VYALLOC(t,r) (t *)vyAlloc( r )

#define BUFSIZE 512

char
   *VYMODULEINIT = "vyModuleInit";

char
   *VYNOMEM = "Out of memory",
   *NOCONTEXT = "Missing context",
   *NOIMPARGS = "Missing implementations arguments",
   *NOIMPDEST = "Missing implementation destination",
   *NOIMPL = "Missing implementation: %s.%s",
   *NOINTF = "Missing interface name",
   *NOMODULE = "Cannot load module: %s: %s",
   *NOMODULEINIT = "Missing vyModuleInit function: %s",
   *NONATIVE = "Missing native name",
   *NOREPR = "Missing representation: %s.%s",
   *NOSET = "This representation cannot be assigned",
   *NOTYPE = "Missing type",
   *NOVER = "Missing version",
   *UNIMP = "Unknown implementation: %s@%d",
   *UNNATIVE = "Unknown native type: %s",
   *UNREPR = "Unknown representation: %s";

char vyBuf[BUFSIZE];

struct VyRepr {
   size_t size;
   VySetter set;
   VyDestr destr;
};


struct VyArgs {
   VyRepr repr;
   VyCStr intf;
   VyVer ver;
   VySm types;
   VySm funcs;
};

struct VyContext {
   VyRepr repr;
   Vy vy;
   int nimpls;
   VyArgs * impls;
   VySm natvs;
};

struct Vy {
   VyRepr repr;
   VyContext context;
   VySm modules;
};

void vyDestroyContext( VyContext ctx ) {
   ctx->impls = REALLOC( ctx->impls, 0 );
   vySmClear( &ctx->natvs );
}

void vyNoSet( VyAny * dest, VyAny val ) {
   vyThrow( NOSET );
}

void vyDestroyVy( Vy vy ) {
   vySmClear( & vy->modules );
   vyFree( vy->context );
}

struct VyRepr vyrVy = {
   .size = sizeof(struct Vy),
   .set = vyNoSet,
   .destr = (VyDestr)vyDestroyVy
};

struct VyRepr vyrContext = {
   .size = sizeof(struct VyContext),
   .set = vyNoSet,
   .destr = (VyDestr)vyDestroyContext
};


VyCStr vyStr( VyCStr s ) {
   return s ? s : "";
}

bool vySameStr( VyCStr a, VyCStr b ) {
   if ( !a || !b ) return false;
   return 0 == strcmp( a, b );
}

void vyDestroyArgs( VyArgs ia ) {
   vySmClear( &ia->types );
   vySmClear( &ia->funcs );
}

struct VyRepr vyrVyArgs = {
   .size = sizeof(struct VyArgs),
   .set = vyNoSet,
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

VyPtr vyAllocRef( VyRepr r ) {
   VyPtr ret = vyAlloc( r );
   ((VyRefCount)ret)->ref = 0;
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
   vySmInit( & ret->modules );
   VyContext ctx = ret->context = vyContextCreate( ret );
   vyInitCore( ctx );
   vyInitUtil( ctx );
   vyInitGeom( ctx );
   vyInitUi( ctx );
   vyLoadModule( ctx, "vysdl" );
   return ret;
}

void vyLoadModule( VyContext ctx, VyCStr name ) {
   VySm * mods = & ctx->vy->modules;
   if ( 0 <= vySmFind( mods, name ))
      return;
   void * mod = vyaLoadLibrary( name );
   if ( ! mod ) {
      snprintf( vyBuf, BUFSIZE, NOMODULE, name, vyaLibraryError() );
      vyThrow( vyBuf );
   }
   void * init = vyaLibraryFunc( mod, VYMODULEINIT );
   if ( ! init ) {
      snprintf( vyBuf, BUFSIZE, NOMODULEINIT, name );
      vyThrow( vyBuf );
   }
   vySmAdd( mods, name, mod );
   ((VyModuleInit)init)( ctx );
}

VyContext vyContext( Vy vy ) {
   return vy->context;
}

VyRepr vyNative( VyContext ctx, VyCStr name ) {
   int i = vySmFind( &ctx->natvs, name );
   if ( 0 <= i )
      return (VyRepr)ctx->natvs.ptrs[i];
   snprintf( vyBuf, BUFSIZE, UNNATIVE, name );
   vyThrow( vyBuf );
}


/// egy függvény implementációja
VyPtr vyGetFunc( VyArgs args, VyCStr name ) {
   int i = vySmFind( &args->funcs, name );
   if ( 0 <= i )
      return args->funcs.ptrs[i];
      else return NULL;
}

/// illeszkednek-e az implementációk
bool vyMatchesImpl( VyArgs args, VyArgs other ) {
   if ( args->ver != other->ver ) return false;
   if ( ! vySameStr( args->intf, other->intf )) return false;
   unsigned nt = args->types.count;
   if ( nt != other->types.count ) return false;
   for ( int i=0; i<nt; ++i) {
      VyRepr r = (VyRepr)args->types.ptrs[i];
      VyRepr ro = vyGetRepr( other, args->types.strs[i] );
      if ( ! ro ) return false;
      if ( NULL != r && r != ro ) return false;
   }
   unsigned nf = args->funcs.count;
   for ( int i=0; i<nf; ++i) {
      if ( ! vyGetFunc( other, args->funcs.strs[i] ) )
         return false;
   }
   return true;
}

/// implementáció hattatása
void vyApplyImpl( VyArgs args, VyArgs dest, VyPtr * ptrs ) {
   for (unsigned i=0; i < dest->types.count; ++i) {
      if ( ! dest->types.ptrs[i] )
         dest->types.ptrs[i] = vyGetRepr( args, dest->types.strs[i] );
   }
   unsigned nf = dest->funcs.count;
   for (unsigned i=0; i < nf; ++i)
      ptrs[i] = vyGetFunc( args, dest->funcs.strs[i] );
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

VyRepr vyRepr( size_t size, VySetter set, VyDestr destr ) {
   VyRepr ret = REALLOC( NULL, sizeof( struct VyRepr ) );
   ret->size = size;
   ret->set = set;
   ret->destr = destr;
   return ret;
}


VyRepr vyGetRepr( VyArgs ia, VyCStr type ) {
   if ( ! ia ) vyThrow( NOIMPARGS );
   if ( ! type ) vyThrow( NOTYPE );
   int i = vySmFind( &ia->types, type );
   if ( 0 <= i )
      return (VyRepr)ia->types.ptrs[i];
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
   vySmInit( & ret->types );
   vySmInit( & ret->funcs );
   return ret;
}


void vyArgsType( VyArgs ia, VyCStr type, VyRepr repr ) {
   int i = vySmFind( &ia->types, type );
   if ( 0 <= i )
	  ia->types.ptrs[i] = repr;
	  else vySmAdd( &ia->types, type, repr );
}

void vyArgsFunc( VyArgs ia, VyCStr func ) {
   vyArgsImpl( ia, func, NULL );
}

void vyArgsImpl( VyArgs ia, VyCStr func, VyPtr impl ) {
   int i = vySmFind( & ia->funcs, func );
   if ( 0 <= i )
	  ia->funcs.ptrs[i] = impl;
      else vySmAdd( & ia->funcs, func, impl );
}

/// reprezentációk és implementációk ellenőrzése
void vyCheckImplem( VyArgs ia ) {
   for (int i = ia->types.count-1; 0 <= i; --i ) {
	  if ( ! ia->types.ptrs[i] ) {
         snprintf( vyBuf, BUFSIZE, NOREPR, ia->intf, 
            vyStr( ia->types.strs[i] ));
         vyThrow( vyBuf );
      }
   }
   for (int i = ia->funcs.count-1; 0 <= i; --i ) {
	  if ( ! ia->funcs.ptrs[i] ) {
		 snprintf( vyBuf, BUFSIZE, NOIMPL, ia->intf,
		    vyStr( ia->funcs.strs[i] ));
		 vyThrow( vyBuf );
      }
   }
}


void vyAddImplem( VyContext ctx, VyArgs ia ) {
   unsigned n = ctx->nimpls+1;
   VyArgs * impls = REALLOC( ctx->impls, n*sizeof(VyPtr) );
   if ( ! impls ) vyThrow( VYNOMEM );
   vyCheckImplem( ia );
   impls[n-1] = ia;
   ctx->impls = impls;
   ctx->nimpls = n;
}

VyRepr vyAddNative( VyContext ctx, VyCStr name, size_t size ) {
   VyRepr ret = vyRepr( size, NULL, NULL );
   vySmAdd( & ctx->natvs, name, ret );
   return ret;
}

void vySetRef( VyAny * dest, VyAny val ) {
   VyRefCount old = *(VyRefCount *)dest;
   if ( old == val )
      return;
   if ( NULL != old ) {
      if ( 0 >= -- old->ref )
         vyFree( old );
   }
   *dest = val;
   ++ ((VyRefCount)val)->ref;
}

void vySetter( VyAny * dst, VyAny src ) {
   ((VyRepr)dst)->set( dst, src );
}


