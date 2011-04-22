from django.contrib.auth.decorators import login_required
from django.db.transaction import commit_on_success
from django.shortcuts import render_to_response, redirect
from django.template.context import RequestContext

def role_list(request):
    role_list = 'test'
    return render_to_response('role/list.html', {'role_list':role_list }, context_instance = RequestContext(request))

@login_required
def edit_role(request):
    role_list = 'test'
    return render_to_response('role/list.html', {'role_list':role_list })

@login_required
def add_role(request):
    role_list = 'test'
    return render_to_response('role/list.html', {'role_list':role_list })
