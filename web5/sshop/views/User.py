# -*- coding: utf-8 -*-

import tornado.web
from sqlalchemy.orm.exc import NoResultFound
from sshop.base import BaseHandler
from sshop.models import User
import bcrypt
import os

string_blacklist = ('{{', "'", 'script', 'object', 'onerror', 'onload',
    'select', 'from', 'where', 'union', 'os', 'sys', 'open', 'include', 'extend', 'module',
    'timeit', 'subprocess', 'import', 'print', 'curl', 'proc',
    'builtin', 'eval', 'exec', 'input', 'pickle', 'reload')

class UserLoginHanlder(BaseHandler):
    def get(self, *args, **kwargs):
        self.application._generate_captcha()
        return self.render('login.html', ques=self.application.question, uuid=self.application.uuid)

    def post(self):
        if not self.check_captcha():
            return self.render('login.html', danger=1, ques=self.application.question, uuid=self.application.uuid)
        username = self.get_argument('username')
        password = self.get_argument('password')
        if username and password:
            try:
                user = self.orm.query(User).filter(User.username == username).one()
            except NoResultFound:
                return self.render('login.html', danger=1, ques=self.application.question, uuid=self.application.uuid)
            if user.check(password):
                self.set_secure_cookie('username', user.username)
                self.set_secure_cookie('isvip', str(int(user.isvip)))
                self.redirect('/user')
            else:
                return self.render('login.html', danger=1, ques=self.application.question, uuid=self.application.uuid)


class RegisterHandler(BaseHandler):
    def get(self, *args, **kwargs):
        self.application._generate_captcha()
        return self.render('register.html', ques=self.application.question, uuid=self.application.uuid)

    def post(self, *args, **kwargs):
        if not self.check_captcha():
            return self.render('login.html', danger=1)
        username = self.get_argument('username')
        mail = self.get_argument('mail')
        password = self.get_argument('password')
        password_confirm = self.get_argument('password_confirm')
        invite_user = self.get_argument('invite_user')

        if password != password_confirm:
            return self.render('register.html', danger=1, ques=self.application.question, uuid=self.application.uuid)
        if mail and username and password:
            try:
                user = self.orm.query(User).filter(User.username == username).one()
            except NoResultFound:
                self.orm.add(User(username=username, mail=mail,
                                  password=bcrypt.hashpw(password.encode('utf8'), bcrypt.gensalt())))
                self.orm.commit()
                try:
                    inviteUser = self.orm.query(User).filter(User.username == invite_user).one()
                    inviteUser.integral += 0.1
                    self.orm.commit()
                except NoResultFound:
                    pass
                self.redirect('/login')
        else:
            return self.render('register.html', danger=1, ques=self.application.question, uuid=self.application.uuid)


class ResetPasswordHanlder(BaseHandler):
    def get(self, *args, **kwargs):
        self.application._generate_captcha()
        return self.render('reset.html', ques=self.application.question, uuid=self.application.uuid)

    def post(self, *args, **kwargs):
        if not self.check_captcha():
            return self.render('reset.html', danger=1, ques=self.application.question, uuid=self.application.uuid)
        return self.redirect('/login')


class changePasswordHandler(BaseHandler):
    def get(self):
        return self.render('change.html')

    def post(self, *args, **kwargs):
        old_password = self.get_argument('old_password')
        password = self.get_argument('password')
        password_confirm = self.get_argument('password_confirm')
        print old_password, password, password_confirm
        user = self.orm.query(User).filter(User.username == self.current_user).one()
        if password == password_confirm:
            if user.check(old_password):
                user.password = bcrypt.hashpw(password.encode('utf8'), bcrypt.gensalt())
                self.orm.commit()
                return self.render('change.html', success=1)
        return self.render('change.html', danger=1)


class UserInfoHandler(BaseHandler):
    @tornado.web.authenticated
    def get(self, *args, **kwargs):
        user = self.orm.query(User).filter(User.username == self.current_user).one()
        isvip = self.get_secure_cookie('isvip') != '0'
        return self.render('user.html', user=user, isvip=isvip)

    @tornado.web.authenticated
    def post(self, *args, **kwargs):
        user = self.orm.query(User).filter(User.username == self.current_user).one()
        isvip = self.get_secure_cookie('isvip') != '0'
        bio = self.get_argument('bio', '')

        if not isvip:
            return self.render('user.html', danger=1, user=user, isvip=isvip)

        if any(b in bio.lower() for b in string_blacklist):
            return self.render('user.html', danger=1, user=user, isvip=isvip)

        if not os.path.isdir('userbio'):
            os.mkdir('userbio')

        with open('userbio/' + str(user.id) + '.html', 'w') as f:
            f.write(bio)

        return self.render('user.html', success=1, user=user, isvip=isvip)

class BioHandler(BaseHandler):
    @tornado.web.authenticated
    def get(self, *args, **kwargs):
        user = self.orm.query(User).filter(User.username == self.current_user).one()
        isvip = self.get_secure_cookie('isvip') != '0'

        if not isvip:
            self.redirect('/user')

        try:
            return self.render('../../userbio/' + str(user.id) + '.html')
        except:
            return self.write('oops!!!')

class UserLogoutHandler(BaseHandler):
    @tornado.web.authenticated
    def get(self, *args, **kwargs):
        self.clear_cookie('username')
        self.redirect('/login')
