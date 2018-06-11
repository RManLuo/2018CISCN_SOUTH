# -*- coding: utf-8 -*-

import os
import sys
import subprocess

import tornado.web
from sshop.base import BaseHandler

class DebugHandler(BaseHandler):
    def get(self, *args, **kw):
        info = self.get_argument('info', '')

        if info == 'data':
            data = ''
            data += '系统测试信息\n\nTODO：管理员记得在生产环境部署的时候删掉\n\n'
            data += '系统信息：\n' + subprocess.check_output('uname -a', shell=True) + '\n'
            data += 'Python 信息：\n' + str(sys.version_info) + '\n\n'
            data += '工作目录：\n' + os.getcwd() + '\n\n'

            with open('./sshop/settings.py') as f:
                data += '<!-- ' + '配置信息：\n' + f.read() + '-->'

            return self.write(data)
        else:
            return self.render('debug.html')

class SourceHandler(BaseHandler):
    def get(self, *args, **kw):
        with open('9adeb9ab5c8607df825eb98222b030f9.zip', 'rb') as f:
            self.write(f.read())

        self.set_header('Content-Type', 'application/octet-stream')
