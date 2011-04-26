from django.conf.urls.defaults import patterns, url


urlpatterns = patterns('role.views',
    url(r'^list/$',
		'role_list', 
		name='role_list'),

    url(r'^add/$', 
		'add_role', 
		name='add_role'),

    url(r'^(?P<role_id>\d+)/edit/$', 
		'edit_role', 
		name='edit_role'),
	)
