from django.conf.urls.defaults import patterns, url
from . import views

urlpatterns = patterns('',
    url(r'^list/$',
		views.story_list, 
		name='story_list'),

    url(r'^add/$', 
		views.add_story, 
		name='add_story'),

    url(r'^edit/(?P<story_id>\d+)/$', 
		views.edit_story, 
		name='edit_story'),
	)
