from django.conf.urls.defaults import patterns, url
from . import views

urlpatterns = patterns('mobile.views',
    url(r'^$',
		views.index, 
		name='m_index'),
        
    url(r'^show/(?P<story_id>\d+)/$', 
        views.show_story, 
        name='m_show_story'),
	)
