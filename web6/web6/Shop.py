#! /usr/bin/env python 2.7 (62211)
#coding=utf-8
# Compiled at: 2018-05-23 22:59:36
#Powered by BugScaner
#http://tools.bugscaner.com/
#如果觉得不错,请分享给你朋友使用吧!
import tornado.web
from sqlalchemy.orm.exc import NoResultFound
from sshop.base import BaseHandler
from sshop.models import Commodity, User
from sshop.settings import limit
from pickle import loads, dumps
from base64 import b64decode, b64encode
 
class ShopIndexHandler(BaseHandler):
 
    def get(self, *args, **kwargs):
        return self.redirect('/shop')
 
 
class ShopListHandler(BaseHandler):
 
    def get(self):
        page = self.get_argument('page', 1)
        page = int(page) if int(page) else 1
        commoditys = self.orm.query(Commodity).filter(Commodity.amount > 0).order_by(Commodity.price.desc()).limit(limit).offset((page - 1) * limit).all()
        return self.render('index.html', commoditys=commoditys, preview=page - 1, next=page + 1, limit=limit)
 
 
class ShopDetailHandler(BaseHandler):
 
    def get(self, id=1):
        try:
            commodity = self.orm.query(Commodity).filter(Commodity.id == int(id)).one()
        except NoResultFound:
            return self.redirect('/')
 
        return self.render('info.html', commodity=commodity)
 
 
class ShopPayHandler(BaseHandler):
 
    @tornado.web.authenticated
    def post(self):
        try:
            price = self.get_argument('price')
            user = self.orm.query(User).filter(User.username == self.current_user).one()
            user.integral = user.pay(float(price))
            self.orm.commit()
            return self.render('pay.html', success=1)
        except:
            return self.render('pay.html', danger=1)
 
 
class ShopCarHandler(BaseHandler):
 
    @tornado.web.authenticated
    def get(self, *args, **kwargs):
        buycar = self.get_secure_cookie('commodity_buycar')
        if buycar:
            buycar = loads(b64decode(buycar))
            commodities = []
            price = 0
            i = 1
            for one in buycar:
                commodity = self.orm.query(Commodity).filter(Commodity.id == one).one()
                commodity.count = buycar[one]
                commodity.i = i
                commodity.prices = int(buycar[one]) * int(commodity.price)
                price += int(buycar[one]) * int(commodity.price)
                commodities.append(commodity)
                i += 1
 
            return self.render('shopcar.html', commodities=commodities, price=price)
        return self.render('shopcar.html')
 
    @tornado.web.authenticated
    def post(self, *args, **kwargs):
        try:
            price = self.get_argument('price')
            user = self.orm.query(User).filter(User.username == self.current_user).one()
            res = user.pay(float(price))
            if res:
                user.integral = res
                self.orm.commit()
                self.clear_cookie('commodity_buycar')
                return self.render('shopcar.html', success=1)
        except Exception as ex:
            print str(ex)
 
        return self.redirect('/shopcar')
 
 
class ShopCarAddHandler(BaseHandler):
 
    def post(self, *args, **kwargs):
        id = self.get_argument('id')
        buycar = self.get_secure_cookie('commodity_buycar')
        if buycar:
            buycar = loads(b64decode(buycar))
        else:
            buycar = {}
        if id in buycar:
            buycar[id] += 1
        else:
            buycar[id] = 1
        print buycar
        self.set_secure_cookie('commodity_buycar', b64encode(dumps(buycar)))
        return self.redirect('/shopcar')
 
 
class SecKillHandler(BaseHandler):
 
    def get(self, *args, **kwargs):
        return self.render('seckill.html')
 
    def post(self, *args, **kwargs):
        try:
            id = self.get_argument('id')
            commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
            commodity.amount -= 1
            self.orm.commit()
            return self.render('seckill.html', success=1)
        except:
            return self.render('seckill.html', danger=1)