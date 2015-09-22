# -*- coding: utf-8 -*-

from django.core.paginator import Paginator
from django.shortcuts import render, get_object_or_404
from django.template.response import TemplateResponse

import json

from onlinegalgame.settings import MEDIA_URL
from .models import Story, Scene, Role, Background

def _story_images(story):
    role_names, background_names, images = set(), set(), []
    scenes = story.scene_set.all()
    for scene in scenes:
        for command in json.loads(scene.commands):
            try:
                if command.startswith('sp "role"'):
                    name = json.loads(command[10:])['name']
                    role_names.add(name)
                if command.startswith('sp "bg"'):
                    name = json.loads(command[8:])['name']
                    background_names.add(name)
            except:
                pass

    images = list(Role.objects.filter(name__in=role_names).values_list('image', flat=True)) + list(Background.objects.filter(name__in=background_names).values_list('image', flat=True))
    images = [MEDIA_URL + url for url in images]
    return images


def story_create_or_edit(request, story_id=None):
    context = {}
    if story_id:
        context['story'] = get_object_or_404(Story, pk=story_id)
    return TemplateResponse(request, 'story/create.html', context)

def story_play(request, story_id):
    story = get_object_or_404(Story, pk=story_id)
    context = {
        'story' : story,
        'images' : json.dumps(_story_images(story)),
    }
    return TemplateResponse(request, 'story/play.html', context)

def story_list(request):
    paginator = Paginator(Story.objects.all(), 10)
    page = request.GET.get('page', 1)
    stories = paginator.page(page)

    context = {
        'paginator': paginator,
        'stories': stories,
    }
    return TemplateResponse(request, 'story/list.html', context)