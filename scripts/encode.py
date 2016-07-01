from binascii import hexlify, unhexlify
from simplecrypt import encrypt, decrypt
#plaintext = "alper"
#ciphertext = encrypt('password', plaintext)
ciphertext = unhexlify("73630002dd856b9ddd0d96e79259f35548084cbc9f4e5839d43b4718725cab9ed24b7d6d96344d2ad67aa641305fb7e89c1a82e29fd8fdd64ae612a524791389cf31438b2c21853da6")
#print "c:"+hexlify(ciphertext)
mystring = decrypt('password', ciphertext).decode('utf8')
print "p:"+mystring

