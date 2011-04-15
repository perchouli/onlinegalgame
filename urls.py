from django.conf.urls.defaults import patterns, include, url
from views import index
# Uncomment the next two lines to enable the admin:
from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
    # Examples:
    # url(r'^$', 'onlinegalgame.views.home', name='home'),
    url(r'^$', index, name='home'),
    
    # url(r'^onlinegalgame/', include('onlinegalgame.foo.urls')),
    url(r'^accounts/', include('onlinegalgame.accounts.urls')),

	# Uncomment the admin/doc line below to enable admin documentation:
    url(r'^admin/doc/', include('django.contrib.admindocs.urls')),

    # Uncomment the next line to enable the admin:
    url(r'^admin/', include(admin.site.urls)),
)
