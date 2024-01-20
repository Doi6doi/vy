informal vy.vyb.Primitives @20240117 {

Vy binary primitives
====================

Binary primitives are the binary items which can appear in any
[Containers](#Containers). 

null
----

`$00`: null

It means empty data.

bool
----

`$2b`: true  
`$2d`: false  

Logical values. Can be used as vy.comp.Bool.

int
---

`$30`: the number 0  
`$7C`: the number 1  
`$31_`: integer storable in 8 bits (-128 .. 127)  
`$32${2}`: integer storable in 16 bits (-32768..32767)  
`..`  
`$38${9}`: integer storable in 72 bits  

Integer values. Can be used as any const &dec typed data.

string
------

`$27(size:int)${size}`: ASCII string  
`$22(size:int)${size}`: UTF-8 string  

 - size: number of bytes following

String values. Can be used as any "conststring" typed data. 

bytes
-----

`$25(size:int)${size}`: Binary data  

   size: number of bytes following

Any binary data. Can be used as any "consthex" typed data.

ref
---

`$72(index:$)`: reference with index 0..255  
`$52(index:int)`: reference with any index  

 - index: index of item in the pool (starts at 0)

Reference value. References an item in a predefined pool.

list
----

`$5b(count:int)*{count}`: list of any number of items  

 - count: number of elements

A list of values. 

dict
----

`$7b(count:int)((field:string)(value:*)){count}`: dictionary of items

 - count: number of dictionary item
 - field: name of the field. Can be reference if there is a pool
 - value: value of the field

An object with name-value pairs.

pool
----

`$4f(count:int)(value:*){count}`  

 - count: number of items in pool
 - value: a value in the pool

Predefined constant items which can be referenced later.

