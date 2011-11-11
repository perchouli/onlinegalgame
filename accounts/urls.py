from django.conf.urls.defaults import patterns, url
from django.contrib.auth import views as auth_views

urlpatterns = patterns('accounts.views',
    url(r'^login/$', auth_views.login, {'template_name': 'accounts/login.html'}, name='olgg_login'),
    url(r'^register/$', 'register', name='olgg_register'),
    url(r'^profile/(?P<uid>\d+)/$', 'profile', name='olgg_profile'),
    url(r'^logout/$', auth_views.logout, {'next_page': '/'},name='olgg_logout'),
)
