#include "vy_implem.h"
#include "vy_mem.h"
#include "vy_vector.h"

#include <string.h>
#include <stdio.h>

#define PTRS sizeof( VyAny )
#define STEP 16

struct Vector {
   struct VyRefCount ref;
   struct VyMem mem;
   unsigned size;
};

VyRepr vyrVector;

void vyDestroyVector( VyPtr ) {
   vyThrow("stub vyDestroyVector");
}

static Vector vyVectorCreateVector() {
   Vector ret = vyAlloc( vyrVector );
   vyRefInit( (VyRefCount)ret );
   vyMemInit( & ret->mem, STEP * PTRS );
   ret->size = 0;
   return ret;
}

static void vyVectorInsert( Vector v, unsigned at , VyAny x ) {
   if ( v->size < at )
     vyThrow( "Insert after size" );
   if ( v->mem.size < PTRS * (v->size+1) )
      vyMemResize( & v->mem, v->mem.size + STEP*PTRS );
   void *d = v->mem.data;
   memmove( d + PTRS * at+1, d + PTRS*at, PTRS * (v->size - at));
   VyAny * a = ((VyAny *)d)+at;
   (*a) = NULL;
   vySet( a, x );
   ++ (v->size);
}

static void vyVectorRemove( Vector, unsigned ) {
   vyThrow("stub vyVectorRemove");
}

static unsigned vyVectorLength( Vector v ) {
   return v->size;
}

static void vyVectorSetValue( Vector, unsigned, VyAny ) {
   vyThrow("stub vyVectorSetValue");
}

static VyAny vyVectorValue( Vector, unsigned ) {
   vyThrow("stub vyVectorValue");
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

