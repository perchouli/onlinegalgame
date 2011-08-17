from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.template.context import RequestContext
from django.http import HttpResponse
from django.core import serializers

from onlinegalgame.role.models import RoleEvent
from onlinegalgame.story.models import StoryEvent

def home(request):
    events = list(RoleEvent.objects.all()) + list(StoryEvent.objects.all())
    ctx = {
        'events' : events
    }
    print request.session.session_key
    return render_to_response('index.html', ctx, context_instance = RequestContext(request))

def imxml(request):
    #config 
    im_enable = True
    im_float = True
    im_siteid = 'onlinegalgame_com'
    enablesitekey = ''

    #
    querytype = request.GET.get('query')
    print querytype
    if querytype == None:
        return HttpResponse('Invalid query paramter')
    if querytype == 'siteprofile':
        return HttpResponse("<imxml encodeing=\"utf-8\"><software>custom</software><language>utf-8</language><sitename>Onlinegalgame</sitename></imxml>",mimetype = 'application/xml')
    #return HttpResponse(im_siteid)
