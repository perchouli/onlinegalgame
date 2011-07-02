#-*- coding:utf-8 -*-
from django.contrib.auth.decorators import login_required
from django.db.transaction import commit_on_success
from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.http import HttpResponse, Http404
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User
from django.core.paginator import Paginator

from onlinegalgame.role.models import UserRole, UserRoleDress, LinkRole

import md5

def role_list(request):
    role_list = UserRole.objects.all()
    paginator = Paginator(role_list,9)
    try:
        page = int(request.GET.get('page',1))
    except ValueError:
        page = 1
    try:
        role_list = paginator.page(page)
    except:
        role_list = paginator.page(paginator.num_pages)
    link_role_list = []
    session = ''
    if request.user.is_authenticated():
        uid = request.session['_auth_user_id']
        session = { 
        'id' : uid,
    }
        all_link_role = LinkRole.objects.filter(author=uid)
        for link_role in all_link_role:
            link_role_list.append(link_role.linkrole.id)
    ctx = {
        'role_list' : role_list,
        'session' : session,
        'link_role_list' : link_role_list
    }
    return render_to_response('role/list.html', ctx, context_instance = RequestContext(request))

@csrf_exempt   
@login_required
def edit_role(request, role_id):
    if request.method == 'POST':
        data = request.POST
        role_image = ''
        try:
            request.FILES['role_image']
        except:
            pass
        else:
            role_image = request.FILES['role_image']
        userrole            = UserRole.objects.get(id=role_id)
        userrole.name       = data['rolename']
        userrole.birthday   = data['birthday']
        userrole.gender     = data['gender']
        userrole.relation   = data['relation']
        userrole.profile    = data['profile']
        userrole.resume     = data['resume']
        userrole.image      = role_image
        userrole.save()
        return redirect( '/role/list' )
    else:
        role = UserRole.objects.get(id=role_id)
        cloth_list = UserRoleDress.objects.filter(category='cloth')
        hair_list = UserRoleDress.objects.filter(category='hair')
        return render_to_response('role/view.html', {'role_id': role_id, 'role':role, 'cloth_list' : cloth_list,  'hair_list' : hair_list }, context_instance = RequestContext(request))

@csrf_exempt
@login_required
def add_role(request):
    if request.method == 'POST':
        uid = request.session['_auth_user_id']
        role_image = ''
        role_profile = ''
        
        data = request.POST
        try:
            request.FILES['role_image']
        except:
            role_profile = data['profile']
        else:
            role_image = request.FILES['role_image']
            role_profile = ''
        userrole = UserRole (
            name            = data['rolename'],
            birthday        = data['birthday'],
            gender          = data['gender'],
            relation		= data['relation'],
            resume          = data['resume'],
            author          = User.objects.get(id=uid),
            profile         = role_profile,
            image           = role_image,
        )
        userrole.save()
        
        return redirect( '/role/list' )
    else:
        cloth_list = UserRoleDress.objects.filter(category='cloth')
        hair_list = UserRoleDress.objects.filter(category='hair')
        return render_to_response('role/view.html', {'cloth_list' : cloth_list,  'hair_list' : hair_list}, context_instance = RequestContext(request))


@login_required
def link_role(request):
    if request.method == 'GET':
        uid = request.session['_auth_user_id']
        role_id = int(request.GET.get('role_id'))
        linkrole = LinkRole (
            linkrole        = UserRole.objects.get(id=role_id),
            author          = User.objects.get(id=uid),
            token           = md5.new(str(uid)+str(role_id)).hexdigest(),
        )
        linkrole.save()
        return HttpResponse('Success')

        
@login_required
def unlink_role(request):
    if request.method == 'GET':
        uid = request.session['_auth_user_id']
        linkrole = int(request.GET['role_id'])

        linkrole = LinkRole.objects.get(author=uid,linkrole=linkrole)
        linkrole.delete()
        return HttpResponse('Success')
