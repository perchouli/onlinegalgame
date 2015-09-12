"""onlinegalgame URL Configuration

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/1.8/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  url(r'^$', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  url(r'^$', Home.as_view(), name='home')
Including another URLconf
    1. Add an import:  from blog import urls as blog_urls
    2. Add a URL to urlpatterns:  url(r'^blog/', include(blog_urls))
"""
from django.conf.urls import include, url, static
from django.conf import settings
from django.contrib import admin

from onlinegalgame.views import home
import story.urls

from story.api import BackgroundResource, RoleResource, SceneResource, StoryResource

urlpatterns = [
    url(r'^admin/', include(admin.site.urls)),
    url(r'^$', home, name='home'),

    url(r'^stories/', include(story.urls)),

    url(r'^api/backgrounds/', include(BackgroundResource.urls())),
    url(r'^api/roles/', include(RoleResource.urls())),
    url(r'^api/scenes/', include(SceneResource.urls())),
    url(r'^api/stories/', include(StoryResource.urls())),
] + static.static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
