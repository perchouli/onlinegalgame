from django.conf.urls.defaults import patterns, url
from django.contrib.auth import views as auth_views
from . import views


urlpatterns = patterns('',
    url(r'^login/$',
		auth_views.login, 
		{'template_name': 'accounts/login.html'}, 
		name='olgg_login'),

    url(r'^register/$', 
		views.register, 
		name='olgg_register'),

    url(r'^friends/$', 
		views.friends, 
		name='olgg_friends'),

    url(r'^logout/$', 
		auth_views.logout,
		{'template_name': 'index.html'}, 
		name='olgg_logout'),
	)
