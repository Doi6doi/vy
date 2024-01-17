informal vy.vyb.Containers @20240117 {

Vy binary containers
====================

Containers can be built from binary [Primitives](#Primitives). 
All structured data forms a container in binary format.

Containers have the following format:

$7b(type:string)...

   type: container type as string. It can also be a reference to a string
      or a type

Bin
---

A Vy Bin file contains vy compiled data. It can contain executable
code, libraries, definitions, resources, text, or any other data.

$7b'vyb.Bin'(pool:Pool)(list:List)

   pool: constant pool which later can be referenced
   list: list of items of the binary file. An item can be 
      any of: Interface...
      



