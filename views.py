from django.shortcuts import render_to_response
from django.contrib.auth.decorators import login_required
from django.template.context import RequestContext

@login_required
def index(request):
    return render_to_response('index.html', context_instance=RequestContext(request))

