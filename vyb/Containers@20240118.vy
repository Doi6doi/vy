informal vyb.Containers @20240118 {

Vy binary containers
====================

Containers can be built from binary [Primitives](#Primitives). 
All structured data forms a container in binary format.

Containers have the following format:

    $7d(type:string)(ver:ver)(data:*)

 - `type`: container type as string. It can also be a reference to a string
      or a type
 - `ver`: version of the type
 - `data`: data of container

Containers defined by Vy:

 - [Bin](#Bin): VyBin container for vy binary data

}
