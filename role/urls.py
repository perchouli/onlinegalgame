from django.conf.urls.defaults import patterns, url
from . import views

urlpatterns = patterns('role.views',
    url(r'^list/$',
		views.role_list, 
		name='role_list'),

    url(r'^add/$', 
		views.add_role, 
		name='add_role'),

    url(r'^edit/(?P<role_id>\d+)/$', 
		views.edit_role, 
		name='edit_role'),
	)
