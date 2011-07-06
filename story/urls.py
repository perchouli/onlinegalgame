from django.conf.urls.defaults import patterns, url, include
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
        
    url(r'^show/(?P<story_id>\d+)/$', 
        views.show_story, 
        name='show_story'),

    url(r'^story_upload/(?P<user_id>\d+)$', 
        views.upload, 
        name='story_upload'),
        
    url(r'^story_upload_check/$', 
        views.upload_check, 
        name='story_upload_check'),
	)
