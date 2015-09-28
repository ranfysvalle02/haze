# oblivious
Zero knowledge.

oblivious 0.0.1 Alpha - 

==== THIS IS ALPHA SOFTWARE - USE AT YOUR OWN RISKS ====

oblivious began while trying to further modularize a fork of ZeroBin I've been working on (Haze - www.hazedaily.com);

While the purpose of Haze is end-to-end encrypted communication (client-to-client encryption) - oblivious takes care of the back-end process. The oblivious system itself is built in a very simple, easy to understand way. When you create a new entry, all you are doing is creating a file (following a particular folder/naming structure); The contents of the file will be the contents of your entry (but encrypted with a server key); This entry can have certain attributes like expiration time, burnafterreading, and open-discussions(comments). If you are the creator of an entry, you will get a special 'delete token' that let's you remove the entry before its expiration date. 

Oblivious also provides a mechanism to 'invite' users into entries, allows users to comment on entries, add images to content or comments, password protect specific content, and even specify expiration times. This feature-set is sure to expand in the future.

Oblivious includes a default front-end, to show how the system could be used to provide end-to-end encryption to its users. The default front-end is basically a different theme for the Haze front-end - and encapsulates a lot of the magic into easy to use javascript API. Here is a live demo of oblivious: http://www.fabian-valle.com/oblivious/

The oblivious system is built on the Slim framework - a microframework for PHP.

http://www.slimframework.com/



=== ORIGINS ===

oblivious has its origins in ZeroBin - a minimalist, opensource online pastebin where the server 
has zero knowledge of pasted data. 

More information on the project page:

http://sebsauvage.net/wiki/doku.php?id=php:zerobin


------------------------------------------------------------------------------
------------------------------------------------------------------------------

Copyright (c) 2015 Fabian Valle (www.fabian-valle.com)

This software is provided 'as-is', without any express or implied warranty.
In no event will the authors be held liable for any damages arising from 
the use of this software.

Permission is granted to anyone to use this software for any purpose, 
including commercial applications, and to alter it and redistribute it 
freely, subject to the following restrictions:

    1. The origin of this software must not be misrepresented; you must 
       not claim that you wrote the original software. If you use this 
       software in a product, an acknowledgment in the product documentation
       would be appreciated but is not required.

    2. Altered source versions must be plainly marked as such, and must 
       not be misrepresented as being the original software.

    3. This notice may not be removed or altered from any source distribution.

------------------------------------------------------------------------------
------------------------------------------------------------------------------

Copyright (c) 2012 Sébastien SAUVAGE (sebsauvage.net)

This software is provided 'as-is', without any express or implied warranty.
In no event will the authors be held liable for any damages arising from 
the use of this software.

Permission is granted to anyone to use this software for any purpose, 
including commercial applications, and to alter it and redistribute it 
freely, subject to the following restrictions:

    1. The origin of this software must not be misrepresented; you must 
       not claim that you wrote the original software. If you use this 
       software in a product, an acknowledgment in the product documentation
       would be appreciated but is not required.

    2. Altered source versions must be plainly marked as such, and must 
       not be misrepresented as being the original software.

    3. This notice may not be removed or altered from any source distribution.

------------------------------------------------------------------------------
