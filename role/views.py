#-*- coding:utf-8 -*-
from django.contrib.auth.decorators import login_required
from django.db.transaction import commit_on_success
from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User
from onlinegalgame.role.models import UserRole, UserRoleDress

def role_list(request):
    role_list = UserRole.objects.all()
    return render_to_response('role/list.html', {'role_list':role_list }, context_instance = RequestContext(request))

@csrf_exempt   
@login_required
def edit_role(request, role_id):
    if request.method == 'POST':
        data = request.POST
        userrole = UserRole.objects.get(id=role_id)
        userrole.name = data['rolename']
        userrole.birthday = data['birthday']
        userrole.gender = data['gender']
        userrole.relation = data['relation']
        userrole.profile = data['profile']
        userrole.resume = data['resume']
        userrole.save()
        return redirect( '/role/list' )
    else:
        role = UserRole.objects.get(id=role_id)
        cloth_list = UserRoleDress.objects.filter(category='cloth')
        hair_list = UserRoleDress.objects.filter(category='hair')
        return render_to_response('role/edit.html', {'role_id': role_id, 'role':role, 'cloth_list' : cloth_list,  'hair_list' : hair_list }, context_instance = RequestContext(request))

@csrf_exempt
@login_required
def add_role(request):
    if request.method == 'POST':
        uid = request.session['_auth_user_id']
        
        data = request.POST
        #print data
        userrole = UserRole (
            name            = data['rolename'],
            birthday        = data['birthday'],
            gender          = data['gender'],
            relation			= data['relation'],
            resume          = data['resume'],
            profile         = data['profile'],
            author          = User.objects.get(id=uid)
        )
        userrole.save()
        print request.session.items()
        return redirect( '/role/list' )
    else:
        cloth_list = UserRoleDress.objects.filter(category='cloth')
        hair_list = UserRoleDress.objects.filter(category='hair')
        return render_to_response('role/add.html', {'cloth_list' : cloth_list,  'hair_list' : hair_list}, context_instance = RequestContext(request))
