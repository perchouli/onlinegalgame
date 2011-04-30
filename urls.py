from django.conf.urls.defaults import patterns, include, url

from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
    # url(r'^$', 'onlinegalgame.views.home', name='home'),
    url(r'^$', 'django.views.generic.simple.direct_to_template', {'template':'index.html'}),
    
    url(r'^accounts/', include('onlinegalgame.accounts.urls')),
    url(r'^role/', include('onlinegalgame.role.urls')),
    url(r'^story/', include('onlinegalgame.story.urls')),
    
    url(r'^admin/doc/', include('django.contrib.admindocs.urls')),

    url(r'^admin/', include(admin.site.urls)),
    url(r'^openid/', include('django_openid_auth.urls')),
)
