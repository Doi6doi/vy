#include <vy_implem.h>
#include <vy_mem.h>
#include "vy_vector.h"

struct Vector {
   struct VyRefCount ref;
   struct VyMem mem;
};

VyRepr vyrVector;

void vyDestroyVector( VyPtr ) {
   vyThrow("stub vyDestroyVector");
}

static Vector vyVectorCreateVector() {
   Vector ret = vyAlloc( vyrVector );
   vyRefInit( (VyRefCount)ret );
   vyMemInit( & ret->mem, 0 );
}

static void vyVectorInsert( Vector, unsigned, VyAny ) {
   vyThrow("stub vyVectorInsert");
}

static void vyVectorRemove( Vector, unsigned ) {
   vyThrow("stub vyVectorRemove");
}

static unsigned vyVectorLength( Vector ) {
   vyThrow("stub vyVectorLength");
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
   vyrVector = vyRepr( sizeof(struct Vector), vySetRef, vyDestroyVector);
   vyArgsType( args, "Vector", vyrVector );
   vyArgsImpl( args, "createVector", vyVectorCreateVector );
   vyArgsImpl( args, "insert", vyVectorInsert );
   vyArgsImpl( args, "remove", vyVectorRemove );
   vyArgsImpl( args, "length", vyVectorLength );
   vyArgsImpl( args, "setValue", vyVectorSetValue );
   vyArgsImpl( args, "value", vyVectorValue );
   vyAddImplem( ctx, args );
}

