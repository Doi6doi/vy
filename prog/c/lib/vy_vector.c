#include "vy_implem.h"
#include "vy_mem.h"
#include "vy_vector.h"
#include "vy_vec.h"

#include <string.h>
#include <stdio.h>

#define PTRS sizeof( VyAny )
#define STEP 16

struct Vector {
   struct VyRefCount ref;
   struct VyVec vec;
};

VyRepr vyrVector;

static VyAny vyVectorValue( Vector v, unsigned i ) {
   return *(VyAny *)vyVecAt( &v->vec, i );
}

void vyDestroyVector( VyPtr p ) {
   VyVec vv = &((Vector)p)->vec;
   for ( unsigned i=0; i< vv->count; ++i )
      vySet( (VyAny *)vyVecAt( vv, i ), NULL );
   vyVecResize( vv, 0, true );
}


static Vector vyVectorCreateVector() {
   Vector ret = vyAlloc( vyrVector );
   vyRefInit( (VyRefCount)ret );
   vyVecInit( &ret->vec, PTRS, STEP ); 
   return ret;
}

static void vyVectorInsert( Vector v, unsigned i, VyAny x ) {
   VyVec vv = &v->vec;
   unsigned n = vv->count;
   vyVecResize( vv, n+1, false );
   vyVecMove( vv, i, i+1, n-i );
   VyAny * a = (VyAny *)vyVecAt( vv, i );
   *a = NULL;
   vySet( a, x );
}

static void vyVectorRemove( Vector v, unsigned i ) {
   VyVec vv = &v->vec;
   unsigned n = vv->count;
   VyAny * a = (VyAny *)vyVecAt( vv, i );
   vySet( a, NULL );
   vyVecMove( vv, i+1, i, n-i-1 );
   vyVecResize( vv, n-1, false );
}

static unsigned vyVectorLength( Vector v ) {
   return v->vec.count;
}

static void vyVectorSetValue( Vector v, unsigned i, VyAny x ) {
   VyAny * a = (VyAny *)vyVecAt( & v->vec, i );
   vySet( a, x );	
}


void vyInitVector( VyContext ctx ) {
   VYVECTORARGS( ctx, args );
   vyArgsType( args, "Index", vyNative(ctx,"unsigned") );
   vyArgsType( args, "Value", vyNative(ctx,"VyAny") );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyrVector = vyRepr( "Vector", sizeof(struct Vector), vySetRef, vyDestroyVector);
   vyArgsType( args, "Vector", vyrVector );
   vyArgsImpl( args, "createVector", vyVectorCreateVector );
   vyArgsImpl( args, "insert", vyVectorInsert );
   vyArgsImpl( args, "remove", vyVectorRemove );
   vyArgsImpl( args, "length", vyVectorLength );
   vyArgsImpl( args, "setValue", vyVectorSetValue );
   vyArgsImpl( args, "value", vyVectorValue );
   vyAddImplem( ctx, args );
}

