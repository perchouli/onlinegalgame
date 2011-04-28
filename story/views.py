#-*- coding:utf-8 -*-
from django.contrib.auth.decorators import login_required
from django.db.transaction import commit_on_success
from django.shortcuts import render_to_response, redirect, get_object_or_404
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User
from story.models import UserStory
from role.models import UserRole

from datetime import date

def story_list(request):
    story_list = UserStory.objects.all()
    return render_to_response('story/list.html', {'story_list':story_list }, context_instance = RequestContext(request))

@csrf_exempt   
@login_required
def edit_story(request, story_id):

    uid = request.session['_auth_user_id']
    if request.method == 'POST':
        data = request.POST
        userrole = UserStory.objects.get(id=story_id)
        userrole.title = data['title']
        #userrole.date = data['birthday']
        userrole.summary = data['summary']
        userrole.process = data['process']
        userrole.save()
        return redirect( '/story/list' )
    else:
        story = UserStory.objects.get(id=story_id)

        role_list = UserRole.objects.get(author=uid)
        return render_to_response('story/edit.html', {'story_id': story_id, 'story':story, 'role_list':role_list }, context_instance = RequestContext(request))

@csrf_exempt
@login_required
def add_story(request):

    uid = request.session['_auth_user_id']
    if request.method == 'POST':
        
        data = request.POST
        #print data
        userstory = UserStory (
            title        = data['title'],
            cdate         = str(date.today()),
            summary      = data['summary'],
            process      = data['process'],
            author       = User.objects.get(id=uid)
        )
        userstory.save()
        return redirect( '/story/list' )
    else:
        role_list = UserRole.objects.all()
        return render_to_response('story/add.html', {'role_list' : role_list }, context_instance = RequestContext(request))
