from django.conf.urls import include, url
from .views import *

urlpatterns = [
    url(r'^create/$', story_create_or_edit, name='story_create'),
    url(r'^(?P<story_id>\d+)/$', story_play, name='story_play'),
    url(r'^edit/(?P<story_id>\d+)/$', story_create_or_edit, name='story_edit'),
]
