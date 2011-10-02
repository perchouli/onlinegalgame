from django.contrib.auth.decorators import login_required
from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.http import HttpResponse, Http404
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User
from django.core.paginator import Paginator


def index(request):
    
    ctx = {}
    return render_to_response('mobile/index.html', ctx, context_instance = RequestContext(request))

def show_story(request, story_id):
    ctx = {}
    return render_to_response('mobile/show.html', ctx, context_instance = RequestContext(request))