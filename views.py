from django.shortcuts import render_to_response, redirect, get_object_or_404,HttpResponseRedirect
from django.template.context import RequestContext

from onlinegalgame.role.models import RoleEvent
from onlinegalgame.story.models import StoryEvent

def home(request):
    events = list(RoleEvent.objects.all()) + list(StoryEvent.objects.all())
    ctx = {
        'events' : events
    }
    return render_to_response('index.html', ctx, context_instance = RequestContext(request))
