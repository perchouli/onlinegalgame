# -*- coding:utf-8 -*-
from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.template.context import RequestContext
from django.http import HttpResponse
from django.contrib.auth.models import User

from role.models import Role
from story.models import UserStory

def home(request):
    events = {
        'user' : User.objects.all()[0:3],
        'role' : Role.objects.all()[0:3],
        'story': UserStory.objects.all()[0:3],
    }
    '''
    for e in User.objects.all()[0:3]:
        events.append(u'%s 加入了网站' %(e.username))
    for e in Role.objects.all()[0:3]:
        events.append(u'%s 创建了角色 %s' %(e.author.username, e.name))
    for e in UserStory.objects.all()[0:3]:
        events.append(u'%s 编写了场景 %s' %(e.author.username, e.title))
    '''            
    ctx = {
        'events' : events
    }
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
        

