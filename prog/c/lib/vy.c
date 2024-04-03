#include "vy.h"
#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#define REALLOC(p,s) realloc( p, s)
#define VYALLOC(t,r) (t *)vyAlloc( r )

#define BUFSIZE 512

char
   *NOCONTEXT = "Missing context",
   *NOMEM = "Out of memory",
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

typedef void * VyPtr;

struct VyRepr {
   size_t size;
   void (* destroy)( void * );
};

struct VyContext {
};

struct Vy {
   VyRepr repr;
   struct VyContext context;
};

struct VyRepr vyrVy = {
   .size = sizeof(struct Vy),
   .destroy = NULL
};


struct VyImplemArgs {
   VyRepr repr;
   VyCStr intf;
   VyVer ver;
   unsigned ntype;
   VyCStr * types;
   VyRepr * reprs;
   unsigned nfunc;
   VyCStr * funcs;
};

void vyImplemArgsDestroy( void * p ) {
   VyImplemArgs ia = (VyImplemArgs)p;
   ia->types = REALLOC( ia->types, 0 );
   ia->reprs = REALLOC( ia->reprs, 0 );
   ia->funcs = REALLOC( ia->funcs, 0 );
}

struct VyRepr vyrVyImplemArgs = {
   .size = sizeof(struct VyImplemArgs),
   .destroy = vyImplemArgsDestroy
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
   if ( r->destroy )
      r->destroy( obj );
   obj = REALLOC( obj, 0 );
}

Vy vyInit() {
   Vy ret = VYALLOC( struct Vy, & vyrVy );
   if ( ! ret ) vyThrow( NOMEM );
}

VyContext vyContext( Vy vy ) {
   return & vy->context;
}

VyRepr vyNative( VyCStr name ) {
   if ( ! name )
      vyThrow( NONATIVE );
   switch ( *name ) {
      default:
         snprintf( vyBuf, BUFSIZE, UNNATIVE, name );
         vyThrow( vyBuf );
   }
}

VyImplemArgs vyGetImplem( VyContext ctx, VyImplemArgs args, void * dest ) {
   if ( ! ctx ) vyThrow( NOCONTEXT );
   if ( ! args ) vyThrow( NOIMPARGS );
   if ( ! dest ) vyThrow( NOIMPDEST );
   snprintf( vyBuf, BUFSIZE, UNIMP, args->intf, args->ver );
   vyThrow( vyBuf );
}

VyRepr vyGetImplemRepr( VyImplemArgs ia, VyCStr type ) {
   if ( ! ia ) vyThrow( NOIMPARGS );
   if ( ! type ) vyThrow( NOTYPE );
   for (int i=0; i < ia->ntype; ++i) {
      if ( 0 == strcmp( ia->types[i], type ))
         return ia->reprs[i];
   }
   snprintf( vyBuf, BUFSIZE, UNREPR, type );
   vyThrow( vyBuf );
}

VyImplemArgs vyImplemArgs( VyCStr intf, VyVer ver ) {
   if ( ! intf ) vyThrow( NOINTF );
   if ( ! ver ) vyThrow( NOVER );
   VyImplemArgs ret = VYALLOC( struct VyImplemArgs, & vyrVyImplemArgs );
   if ( ! ret ) vyThrow( NOMEM );
   ret->intf = intf;
   ret->ver = ver;
   ret->ntype = 0;
   ret->types = NULL;
   ret->reprs = NULL;
   ret->nfunc = 0;
   ret->funcs = NULL;
   return ret;
}

void vyImplemArgsType( VyImplemArgs ia, VyCStr type, VyRepr repr ) {
   unsigned n = ia->ntype+1;
   VyCStr * types = REALLOC( ia->types, n*sizeof(VyPtr) );
   if ( ! types ) vyThrow( NOMEM );
   VyRepr * reprs = REALLOC( ia->reprs, n*sizeof(VyPtr) );
   if ( ! reprs ) vyThrow( NOMEM );
   types[n-1] = type;
   reprs[n-1] = repr;
   ia->types = types;
   ia->reprs = reprs;
   ia->ntype = n;
}

void vyImplemArgsFunc( VyImplemArgs ia, VyCStr func ) {
   unsigned n = ia->nfunc+1;
   VyCStr * funcs = REALLOC( ia->funcs, n*sizeof(VyPtr) );
   if ( ! funcs ) vyThrow( NOMEM );
   funcs[n-1] = func;
   ia->funcs = funcs;
   ia->nfunc = n;
}

