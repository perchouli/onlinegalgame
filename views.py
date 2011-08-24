from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.template.context import RequestContext
from django.http import HttpResponse
from django.core import serializers

from xml.dom import minidom, Node

from onlinegalgame.role.models import RoleEvent
from onlinegalgame.story.models import StoryEvent
from onlinegalgame.accounts.models import UserProfileEvent

def home(request):
    events = list(RoleEvent.objects.all()) + list(StoryEvent.objects.all()) + list(UserProfileEvent.objects.all())
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

    querytype = request.GET.get('query')
    query = {
        'siteprofile' : '<software>custom</software>'+
                        '<language>utf-8</language>'+
                        '<sitename>Onlinegalgame</sitename>',
        'login' : '<version>3.0.0</version>'+
                  '<sessionvalide>true</sessionvalide>',
        'addbuddy' : '<userkeyvalide>true</userkeyvalide>'+
                     '<addbuddyresult>accepted</addbuddyresult>'
    }
    res = '<imxml encoding="utf-8">%s</imxml>' % query[querytype]
    if querytype == None:
        return HttpResponse('Invalid query paramter')
    else:
        return HttpResponse(res,mimetype='application/xml')
    #elif querytype == 'addbuddy':
    #    return HttpResponse(action[querytype],mimetype='application/xml')
    #if querytype == 'siteprofile':
        

