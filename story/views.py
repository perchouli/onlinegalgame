from django.shortcuts import render, get_object_or_404
from django.template.response import TemplateResponse

import json

from .models import Story, Scene

def story_create_or_edit(request, story_id=None):
    context = {}
    if story_id:
        context['story'] = get_object_or_404(Story, pk=story_id)
    return TemplateResponse(request, 'story/create.html', context)

def story_play(request, story_id):
    story = get_object_or_404(Story, pk=story_id)
    context = {
        'story' : story,
    }
    return TemplateResponse(request, 'story/play.html', context)
