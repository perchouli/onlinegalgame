#-*- coding:utf-8 -*-
from django.contrib.auth.decorators import login_required
from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.http import HttpResponse, Http404
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User
from django.core.paginator import Paginator

from .models import Role, Dress, LinkRole
from .forms import RoleForm
import hashlib, datetime, random

def role_list(request):

    #查询引用的角色，append到角色列表中
    link_role_list = []
    for link_role in LinkRole.objects.filter(author=request.user.id):
        link_role_list.append(link_role.linkrole.id)
    #tag查询
    if request.GET.get('tag'):
        role_list = Role.objects.filter(parent=0).filter(tags__contains=request.GET.get('tag'))
    else:
        role_list = Role.objects.filter(parent=0)
        
    for i in range(len(role_list)):
        if role_list[i].tags:
            role_list[i].tags = role_list[i].tags.split(' ') #角色属性标签
        role_list[i].children = Role.objects.filter(parent=role_list[i].id)
        
        if role_list[i].id in link_role_list:
            role_list[i].islink   = True
        else:
            role_list[i].islink   = False
    
    #分页开始，9个角色为一页
    paginator = Paginator(role_list,9)
    page = request.GET.get('page',1)
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


@login_required
def add_role(request):
    if request.method == 'POST':
        form = RoleForm(request.POST)
        if form.is_valid():
            role = form.save(commit = False)
            role.author = request.user
            #用户是否上传了图片
            if request.FILES.get('image'):
                #若上传图片，则保存图片，清空角色配置,用hash重置文件名
                request.FILES['image'].name = hashlib.sha1(str(datetime.datetime.now())+str(random.random())).hexdigest()
                role.image = request.FILES['image']
                role.profile = ''
            else:
                #若没有，则保存角色配置
                role.profile = request.POST['profile'].replace(' repeat scroll 0% 0% transparent;','').replace('"',"'")
                role.image = ''

        #如果有父角色
            if request.POST.get('parent',0) != 0:
                role.parent_id = int(request.POST['parent'])
            role.save()
            return redirect( '/role/list' )
        else:
            return render_to_response('role/view.html', {'form':form}, context_instance = RequestContext(request))
    else:
        form = RoleForm()
        ctx = {
            #'role_list'     : Role.objects.filter(author=request.user.id).filter(parent=0), #暂时不使用父角色
            'cloth_list'    : Dress.objects.filter(category='cloth'),
            'hair_list'     : Dress.objects.filter(category='hair'),
            'form'          : form
        }
        return render_to_response('role/view.html', ctx, context_instance = RequestContext(request))



@csrf_exempt
@login_required
def edit_role(request, role_id):
    role = get_object_or_404(Role, pk=role_id)
    if request.method == 'POST':
        form = RoleForm(request.POST, instance=role)#不传入instance会新建
        if form.is_valid():
            role = form.save(commit = False)
            #用户是否上传了图片
            if request.FILES.get('image'):
                #若上传图片，则保存图片，清空角色配置,使用原来的文件名
                request.FILES['image'].name = role.image.name
                role.image = request.FILES['image']
                role.profile = ''
            else:
                #若没有，则保存角色配置
                role.profile = request.POST['profile'].replace(' repeat scroll 0% 0% transparent;','').replace('"',"'")
                role.image = ''
                
            role.save()

        return redirect( '/role/list' )
    else:
        form = RoleForm(instance=role)
        ctx = {
            'role' : role,
            'cloth_list' : Dress.objects.filter(category='cloth'),
            'hair_list' : Dress.objects.filter(category='hair'),
            'form' : form
        }
        return render_to_response('role/view.html', ctx, context_instance = RequestContext(request))

@login_required
def link_role(request,islink):
    uid = request.session['_auth_user_id']
    if islink == 'link':
        role_id = int(request.GET.get('role_id'))
        linkrole = LinkRole (
            linkrole        = Role.objects.get(id=role_id),
            author          = User.objects.get(id=uid),
            token           = hashlib.sha1(str(uid)+str(role_id)).hexdigest(),
        )
        linkrole.save()
    else:
        linkrole = int(request.GET['role_id'])
        linkrole = LinkRole.objects.get(author=uid,linkrole=linkrole)
        linkrole.delete()
    return HttpResponse('Success')

def show_role(request, role_id):
    role = Role.objects.get(id=role_id)
    ctx = {
        'role' : role
    }
    return render_to_response('role/show.html', ctx, context_instance = RequestContext(request))
