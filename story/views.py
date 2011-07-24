#-*- coding:utf-8 -*-
from django.contrib.auth.decorators import login_required
from django.db.transaction import commit_on_success
from django.shortcuts import render_to_response, redirect, get_object_or_404
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User
from django.http import HttpResponse
from django.core.paginator import Paginator

from onlinegalgame.story.models import UserStory
from onlinegalgame.role.models import Role, LinkRole
from onlinegalgame.fileupload.models import StoryUpload
from onlinegalgame.settings import PROJECT_PATH

from datetime import date
import json
import os

def story_list(request):
    story_list = UserStory.objects.values('author').distinct()
    user_list = User.objects.all()
    ex = ''
    for i in range(len(user_list)):
        if UserStory.objects.filter(author=user_list[i].id).count() == 0:
            ex = ex + '.exclude(id='+str(user_list[i].id)+')'
    user_list = eval('User.objects'+ex)
    #分页开始，6个故事为一页
    paginator = Paginator(user_list,6)
    try:
        page = int(request.GET.get('page',1))
    except ValueError:
        page = 1
    try:
        user_list = paginator.page(page)
    except:
        user_list = paginator.page(paginator.num_pages)
    #分页结束
    return render_to_response('story/list.html', {'user_list':user_list }, context_instance = RequestContext(request))

@csrf_exempt   
@login_required
def del_story(request):
    if request.method == 'POST':
        data = request.POST
        s = UserStory.objects.get(id=int(data['sid']))
        if s.author.id == request.user.id:
            s.delete()
        return HttpResponse('0')

@csrf_exempt   
@login_required
def edit_story(request):
    if request.method == 'POST':
        data = request.POST
        s = UserStory.objects.get(id=int(data['sid']))
        if s.author.id == request.user.id:
            s.sort = int(data['sort'])
            s.title = data['title']
            s.save()
        return HttpResponse('0')

@csrf_exempt
@login_required
def add_story(request):
    if request.method == 'POST':
        data = request.POST
        try:
            int(data['sort'])
        except ValueError:
            data['sort'] = 1
        try:
            request.FILES['cover_image']
        except:
            request.FILES['cover_image'] = ''
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
        'role_list' : role_list,
        'story_list': UserStory.objects.filter(author=story.author.id).order_by('sort')
    }
    return render_to_response('story/show.html', ctx, context_instance = RequestContext(request))

@csrf_exempt
def upload(request,user_id):
    user_id = int(user_id)
    file_name = str(request.FILES['Filedata'].name);
    file_path = 'static/cg/'+str(user_id)+'/'
    try:
        open(file_path+file_name, 'w')
    except:
        os.mkdir(file_path)
        file_upload = open(file_path+file_name, 'w')
    else:
        file_upload = open(file_path+file_name, 'w')
    file_upload.write(request.FILES['Filedata'].read())
    file_upload.close()
    file_exist = StoryUpload.objects.filter(image=file_path+file_name).count()
    if file_exist == 0:
        upload_file = StoryUpload(
        uid = User.objects.get(id=user_id),
        image = 'static/cg/'+str(user_id)+'/'+request.FILES['Filedata'].name
            )
        upload_file.save()
    return HttpResponse('0')

@csrf_exempt
def upload_check(request,user_id):
    
    print request.FILES['Filedata'].name
    
    return HttpResponse('0')
    #else:
    #    return HttpResponse('1')

@csrf_exempt
def story_upload_update(request):
    user_upload_list = StoryUpload.objects.filter(uid = request.user.id)
    file_list = ''
    for file in user_upload_list:
        file_list += file.image + '|'
    return HttpResponse(file_list)

@csrf_exempt
def delete_story(request):
    pass

def __unicode__(self):
    return u'%s %s %s' % (self.title, self.summary, self.process)
    
