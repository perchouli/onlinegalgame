from django.conf.urls.defaults import patterns, url
from django.contrib.auth import views as auth_views
from accounts.views import register, profile


urlpatterns = patterns('',
    url(r'^login/$',
		auth_views.login, 
		{'template_name': 'accounts/login.html'}, 
		name='olgg_login'),

    url(r'^register/$', 
		register, 
		name='olgg_register'),

    url(r'^profile/$', 
		'accounts.views.profile', 
		name='olgg_profile'),

    url(r'^friends/$', 
		'accounts.views.friends', 
		name='olgg_friends'),

    url(r'^logout/$', 
		auth_views.logout,
		#import logout template
		{'template_name': 'index.html'}, 
		name='olgg_logout'),
	)
