#-*- coding:utf-8 -*-
from django.contrib.auth.decorators import login_required
from django.db.transaction import commit_on_success
from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User
from onlinegalgame.bbs.models import Threads, Replys

def threads_list(request):
    threads_list = Threads.objects.all()
    return render_to_response('bbs/list.html', {'threads_list':threads_list }, context_instance = RequestContext(request))
