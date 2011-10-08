from django.contrib.auth.decorators import login_required
from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.http import HttpResponse, Http404
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User
from django.core.paginator import Paginator

from onlinegalgame.story.models import UserStory
from onlinegalgame.role.models import Role, LinkRole
from onlinegalgame.fileupload.models import StoryUpload
from onlinegalgame.settings import PROJECT_PATH

def index(request):
    
    ctx = {}
    return render_to_response('mobile/index.html', ctx, context_instance = RequestContext(request))

def show_story(request, story_id):
    story = UserStory.objects.get(id=story_id)
    command = story.process
    
    role_list = list(Role.objects.filter(author=story.author.id))
    link_role_list = LinkRole.objects.filter(author=story.author.id)
    #SecurityHash End
    for role in link_role_list:
        role_list.append(role.linkrole)
    ctx = {
        'story'     : story,
        'command'   : command,
        'role_list' : role_list,
    }
    return render_to_response('mobile/show.html', ctx, context_instance = RequestContext(request))
