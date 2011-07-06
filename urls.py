from django.conf.urls.defaults import patterns, include, url
from . import settings

#from django.contrib import admin
#admin.autodiscover()

urlpatterns = patterns('',
    # url(r'^$', 'onlinegalgame.views.home', name='home'),
    url(r'^$', 'django.views.generic.simple.direct_to_template', {'template':'index.html', 'username' : 'Perchouli'}),
    
    #This line in need when run in Apache
    url(r'^static/(?P<path>.*)$', 'django.views.static.serve', {'document_root': settings.STATIC_ROOT}, name="static"),

    #The url about app
    url(r'^accounts/', include('onlinegalgame.accounts.urls')),
    url(r'^role/', include('onlinegalgame.role.urls')),
    url(r'^story/', include('onlinegalgame.story.urls')),
    url(r'^forum/', include('onlinegalgame.forum.urls')),

    #url(r'^admin/doc/', include('django.contrib.admindocs.urls')),
    #url(r'^admin/', include(admin.site.urls)),
    #url(r'^i18n/', include('django.conf.urls.i18n')),
    #url(r'^openid/', include('django_openid_auth.urls')),
)
