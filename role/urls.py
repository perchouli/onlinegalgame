from django.conf.urls.defaults import patterns, url

urlpatterns = patterns('role.views',
    url(r'^list/$', 'role_list', name='role_list'),
    url(r'^add/$', 'add_role', name='add_role'),
    url(r'^edit/(?P<role_id>\d+)/$', 'edit_role', name='edit_role'),
    url(r'^show/(?P<role_id>\d+)/$', 'show_role', name='show_role'),
    url(r'^link/(?P<islink>\S+)/$', 'link_role', name='link_role'),
)
