#-*- coding:utf-8 -*-
from django.contrib.auth.decorators import login_required
from django.db.transaction import commit_on_success
from django.shortcuts import render_to_response, redirect, get_object_or_404
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User
from django.http import HttpResponse
from onlinegalgame.story.models import UserStory
from onlinegalgame.role.models import Role, LinkRole
from onlinegalgame.fileupload.models import StoryUpload
from onlinegalgame.settings import PROJECT_PATH

from datetime import date
import json
import os

def story_list(request):
    story_list = UserStory.objects.all()
    return render_to_response('story/list.html', {'story_list':story_list }, context_instance = RequestContext(request))

@csrf_exempt   
@login_required
def edit_story(request, story_id):

    uid = request.session['_auth_user_id']
    if request.method == 'POST':
        data = request.POST
        userstory = UserStory.objects.get(id=story_id)
        userstory.title = data['title']
        #userrole.date = data['birthday']
        userstory.summary = data['summary']
        userstory.process = data['process']
        userstory.save()
        return redirect( '/story/list' )
    else:
        story = UserStory.objects.get(id=story_id)

        role_list = UserRole.objects.get(author=uid)
        return render_to_response('story/edit.html', {'story_id': story_id, 'story':story, 'role_list':role_list }, context_instance = RequestContext(request))

@csrf_exempt
@login_required
def add_story(request):
    if request.method == 'POST':
        data = request.POST
        try:
            int(data['sort'])
        except ValueError:
            data['sort'] = 1
        userstory = UserStory (
            title           = data['title'],
            cdate           = str(date.today()),
            summary         = data['summary'],
            process         = data['process'],
            sort            = int(data['sort']),
            image           = request.FILES['cover_image'],
            author          = User.objects.get(id=request.user.id)
        )
        userstory.save()
        return redirect( '/story/list' )
    else:
        role_list = list(Role.objects.filter(author=request.user.id))
        link_role_list = LinkRole.objects.filter(author=request.user.id)
        default_bgd_list = os.listdir( os.path.join(PROJECT_PATH,'static/bgd'))
        user_upload_list = StoryUpload.objects.filter(uid = request.user.id)
        story_list = UserStory.objects.filter(author=request.user.id)
        for role in link_role_list:
            role_list.append(role.linkrole)
        ctx = {
            'role_list' : role_list,
            'default_bgd_list' : default_bgd_list,
            'user_upload_list' : user_upload_list,
            'story_list' : story_list
        }
        return render_to_response('story/add.html', ctx, context_instance = RequestContext(request))

def show_story(request, story_id):

    #uid = request.session['_auth_user_id']
    story = UserStory.objects.get(id=story_id)
    command = story.process
    
    role_list = list(Role.objects.filter(author=story.author.id))
    link_role_list = LinkRole.objects.filter(author=story.author.id)

    for role in link_role_list:
        role_list.append(role.linkrole)
    ctx = {
        'story'     : story,
        'command'   : command,
        'role_list' : role_list
    }
    return render_to_response('story/show.html', ctx, context_instance = RequestContext(request))

@csrf_exempt
def upload(request,user_id):
    user_id = int(user_id)
    file_name = str(request.FILES['Filedata'].name);
    try:
        file_upload = open('static/cg/'+str(user_id)+'/'+file_name, 'w')
    except:
        os.mkdir('static/cg/'+str(user_id))
        file_upload = open('static/cg/'+str(user_id)+'/'+file_name, 'w')
    else:
        file_upload = open('static/cg/'+str(user_id)+'/'+file_name, 'w')
    file_upload.write(request.FILES['Filedata'].read())
    file_upload.close()
    upload_file = StoryUpload(
        uid = User.objects.get(id=user_id),
        image = '/static/cg/'+str(user_id)+'/'+request.FILES['Filedata'].name
            )
    upload_file.save()
    return HttpResponse('0')

@csrf_exempt
def upload_check(request):
    return HttpResponse('0')

def __unicode__(self):
    return u'%s %s %s' % (self.title, self.summary, self.process)
    
