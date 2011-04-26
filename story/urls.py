from django.conf.urls.defaults import patterns, url


urlpatterns = patterns('story.views',
    url(r'^list/$',
		'story_list', 
		name='story_list'),

    url(r'^add/$', 
		'add_story', 
		name='add_story'),

    url(r'^(?P<role_id>\d+)/edit/$', 
		'edit_role', 
		name='edit_role'),
	)
