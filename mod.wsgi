import os, sys
sys.path.append("C:/Documents and Settings/Administrator")

os.environ['DJANGO_SETTINGS_MODULE'] = 'onlinegalgame.settings'

import django.core.handlers.wsgi
application = django.core.handlers.wsgi.WSGIHandler()