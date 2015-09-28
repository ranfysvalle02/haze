# haze

Haze 0.0.1 Alpha - www.hazedaily.com

==== THIS IS ALPHA SOFTWARE - USE AT YOUR OWN RISKS ====

Haze is a communication platform focused on privacy and security. It allows users to have 
conversations online while protecting their identity and content (text/images) with 
256 AES end-to-end encryption. It also provides an optional layer of security by letting users
further protect their content with passwords. 

The Haze server's only purpose is to store encrypted content. The server has zero knowledge of 
the data being stored - which means your data is safe even in case of a breach or seizure. **

So how does it work? First, you choose the desired configuration options for your haze entry (image, password protect, expiration, etc.); Then, when the user clicks 'send' - the data gets encrypted with a 256 bit key, which never leaves the client. The server receives only the encrypted content.

If a user has added an image, the server never receives the actual image. Instead, Haze grabs the base64 encoded image data, and encrypts it along with the rest of the haze entry. 

If a user has chosen to password protect his content, his entire haze entry would have an additional layer of AES encryption on top of the 256 bit AES encryption;

Haze requires no user information - The communication can be as anonymous / private as you like. When you comment on a haze-entry, you can choose to enter a nickname, or post anonymously. You can set an 'alias' via the 'Settings' panel to avoid having to manually enter an alias everytime you comment. This will not be stored in the Haze server - it will live with the Blackbook*.

So how can you communicate? When you create a haze entry, the entry data gets stored in your local blackbook. The blackbook will contain your client-side encryption/decryption keys, among other data for the user-experience. This data is never accessed in any way by the server. (end-to-end encryption)

By navigating the 'public' category, you will be able to see all unexpired haze entries. Though users will be able to view entries, the  data will be encrypted using a 256 bit key - so it'd be very difficult for anyone to see the contents of the entry without the key. 

If you have created an encrypted haze entry - the only way anyone else will be able to see the contents is if you send them an invite. An invite code will only work once, and is password protected. If someone gets access to the code, but fails the password - the invite is no longer valid, and another invite will have to be generated. Invites can also be sent for unencrypted entries, and password-protected only entries.

Here is an example of what an ID and Key would look like. You send the server a request for cipher data using an entryID from your blackbook. The server then returns the encrypted information, and at the client-side, the data is decrypted using the 256 bit AES encryption key stored in the blackbook.

entry id
[entry_id=fd9600b8f833c8eb]
256bit key used for the AES encryption
[key=n07LM8bVJAnP//Nreh9Cyt0+2dzJSMCIML9F56HYcBI=]


Haze::BlackBook*
The Haze Blackbook allows users to keep track of their haze-entries. The blackbook contains the collection of haze-entry ids. This collection is stored on the client side only - using HTML5 local storage. The keys are always under the user's control, and he can choose who to send each haze entry to. It also tracks information such as aliases, and meta data to improve the user experience. 

**In the case of a breach or seizure, only past entries would be protected because the hacker
could implant malicious javascript to get your future entries for those using the website directly. People using the Haze mobile app would not fall under this category because the app does not rely on the server for its encryption/decryption, and the server works as only a datastore for encrypted content. 

=== ORIGINS ===

Haze started as a fork of oblivious 0.0.1 Alpha  - at this time it is just a different front-end for the oblivious system, using the oblivious javascript api to its fullest. My hope is to keep both repositories fairly synced and work feature upgrades through improving the oblivious javascript API.

oblivious 0.0.1 Alpha - 

oblivious began while trying to further modularize a fork of ZeroBin I've been working on (Haze - www.hazedaily.com);

While the purpose of Haze is end-to-end encrypted communication (client-to-client encryption) - oblivious takes care of the back-end process. The oblivious system itself is built in a very simple, easy to understand way. When you create a new entry, all you are doing is creating a file (following a particular folder/naming structure); The contents of the file will be the contents of your entry (but encrypted with a server key); This entry can have certain attributes like expiration time, burnafterreading, and open-discussions(comments). If you are the creator of an entry, you will get a special 'delete token' that let's you remove the entry before its expiration date. 

Oblivious also provides a mechanism to 'invite' users into entries, allows users to comment on entries, add images to content or comments, password protect specific content, and even specify expiration times. This feature-set is sure to expand in the future.

Oblivious includes a default front-end, to show how the system could be used to provide end-to-end encryption to its users. The default front-end is basically a different theme for the Haze front-end - and encapsulates a lot of the magic into easy to use javascript API. Here is a live demo of oblivious: http://www.fabian-valle.com/oblivious/

The oblivious system is built on the Slim framework - a microframework for PHP.

http://www.slimframework.com/



=== ORIGINS (oblivious)===

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
