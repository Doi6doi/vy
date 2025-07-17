informal vyb.Obj @25 {

Vy object file
==============

A Vy object file (vyb.Obj) contains binary form of a vy
interface, source, class, or other programming related file.

It has the following format:

$7d("vyb.Obj":string)(@25:ver)(pool:pool)(dict:dict)

The `pool` part contains values which can be used in any
of the following parts.
The `dict` part contains the object data. 
Some fields have special meaning depending of the type,
others mean the same in any object file:

 - "type" (string): Required. The object file type. Can be:
   - "source": A programming language [source](#Source) file in binary form
 - "language" (string): The object file language
