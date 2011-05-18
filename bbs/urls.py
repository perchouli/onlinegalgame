from django.conf.urls.defaults import patterns, url
from . import views

urlpatterns = patterns('bbs.views',
    url(r'^$',
        views.threads_list, 
        name='threads_list'),

    #url(r'^list/$',
	#	views.role_list, 
	#	name='role_list'),

    #url(r'^add/$', 
	#	views.add_role, 
	#	name='add_role'),

    #url(r'^edit/(?P<role_id>\d+)/$', 
	#	views.edit_role, 
	#	name='edit_role'),
	)
