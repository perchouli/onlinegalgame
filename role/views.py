#-*- coding:utf-8 -*-
from django.contrib.auth.decorators import login_required
from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.http import HttpResponse, Http404
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User
from django.core.paginator import Paginator

from onlinegalgame.role.models import Role, RoleDress, LinkRole
from onlinegalgame.role.forms import RoleForm

import hashlib, datetime, random

def role_list(request):
    role_list = Role.objects.filter(parent=0)
    for i in range(len(role_list)):
		role_list[i].tags = role_list[i].tags.split(' ') #角色属性标签
    link_role_list = []
    #获得用户ID
    uid = request.user.id
    #查询引用的角色，append到角色列表中
    all_link_role = LinkRole.objects.filter(author=uid)
    for link_role in all_link_role:
        link_role_list.append(link_role.linkrole.id)
    #分页开始，9个角色为一页
    paginator = Paginator(role_list,9)
    try:
        page = int(request.GET.get('page',1))
    except ValueError:
        page = 1
    try:
        role_list = paginator.page(page)
    except:
        role_list = paginator.page(paginator.num_pages)
    #分页结束

    ctx = {
        'role_list' : role_list,
        'link_role_list' : link_role_list
    }
    return render_to_response('role/list.html', ctx, context_instance = RequestContext(request))


@csrf_exempt
@login_required
def add_role(request):
    if request.method == 'POST':
        data = request.POST
        #用户是否上传了图片
        try:
            request.FILES['role_image']
        except:
            #若没有，则保存角色配置
            role_profile = data['profile']
            role_image = ''
        else:
            #若上传图片，则保存图片，清空角色配置,用hash重置文件名
            request.FILES['role_image'].name = hashlib.sha1(str(datetime.datetime.now())+str(random.random())).hexdigest()
            role_image = request.FILES['role_image']
            role_profile = ''
        #如果有父角色
        if data['parent'] != 0:
            userrole = Role (
                name            = data['rolename'],
                parent_id       = int(data['parent']), 
                author          = User.objects.get(id=request.user.id),
                profile         = role_profile,
                image           = role_image,
        )
        else:
            userrole = Role (
                name            = data['rolename'],
                tags            = data['tags'],
                gender          = data['gender'],
                relation        = data['relation'],
                parent          = data['parent'], 
                resume          = data['resume'],
                author          = User.objects.get(id=request.user.id),
                profile         = role_profile,
                image           = role_image,
        )
        userrole.save()
        return redirect( '/role/list' )
    else:
        ctx = {
            'role_list'     : Role.objects.filter(author=request.user.id).filter(parent=0),
            'cloth_list'    : RoleDress.objects.filter(category='cloth'),
            'hair_list'     : RoleDress.objects.filter(category='hair')
        }
        return render_to_response('role/view.html', ctx, context_instance = RequestContext(request))



@csrf_exempt
@login_required
def edit_role(request, role_id):
    if request.method == 'POST':
        userrole = Role.objects.get(id=role_id)
        data = request.POST
        try:
            request.FILES['role_image']
        except:
            if userrole.image != '' :
                role_image = userrole.image
            else:
                role_image = ''
        else:
            request.FILES['role_image'].name = hashlib.sha1(
                str(datetime.datetime.now())+str(random.random())).hexdigest()
            role_image = request.FILES['role_image']
        userrole.name       = data['rolename']
        userrole.tags       = data['tags']
        userrole.gender     = data['gender']
        userrole.relation   = data['relation']
        userrole.parent     = data['parent']
        userrole.profile    = data['profile']
        userrole.resume     = data['resume']
        userrole.image      = role_image
        userrole.save()
        return redirect( '/role/list' )
    else:
        ctx = {
            'role_list' : Role.objects.filter(author=request.user.id).filter(parent=0),
            'role' : Role.objects.get(id=role_id),
            'cloth_list' : RoleDress.objects.filter(category='cloth'),
            'hair_list' : RoleDress.objects.filter(category='hair')
        }
        return render_to_response('role/view.html', ctx, context_instance = RequestContext(request))

@login_required
def link_role(request):
    if request.method == 'GET':
        uid = request.session['_auth_user_id']
        role_id = int(request.GET.get('role_id'))
        linkrole = LinkRole (
            linkrole        = Role.objects.get(id=role_id),
            author          = User.objects.get(id=uid),
            token           = hashlib.sha1(str(uid)+str(role_id)).hexdigest(),
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
