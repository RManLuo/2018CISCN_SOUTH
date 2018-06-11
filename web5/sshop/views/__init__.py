from Shop import *
from User import *
from Captcha import *
from Debug import *

handlers = [

    (r'/', ShopIndexHandler),
    (r'/shop', ShopListHandler),
    (r'/info/(\d+)', ShopDetailHandler),
    (r'/seckill', SecKillHandler),
    (r'/shopcar', ShopCarHandler),
    (r'/shopcar/add', ShopCarAddHandler),
    (r'/pay', ShopPayHandler),

    (r'/captcha', CaptchaHandler),

    (r'/user', UserInfoHandler),
    (r'/user/change', changePasswordHandler),
    (r'/pass/reset', ResetPasswordHanlder),

    (r'/login', UserLoginHanlder),
    (r'/logout', UserLogoutHandler),
    (r'/register', RegisterHandler),

    (r'/debugggg', DebugHandler),
    (r'/9adeb9ab5c8607df825eb98222b030f9', SourceHandler),
    (r'/bio', BioHandler)
]
