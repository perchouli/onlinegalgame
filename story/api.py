from onlinegalgame.api import Resource

import json
import bleach

from .models import *

class BackgroundResource(Resource):
    def list(self):
        name = self.request.GET.get('name')
        if name:
            return Background.objects.filter(name=name)
        return Background.objects.all()

    def detail(self, pk):
        return Background.objects.get(pk=pk)

class RoleResource(Resource):
    def list(self):
        name = self.request.GET.get('name')
        if name:
            return Role.objects.filter(name=name)

        return Role.objects.all()


class SceneResource(Resource):
    def create(self):
        scene, is_create = Scene.objects.get_or_create(
            name=self.data['name'],
            story_id=self.data['story_id'],
            commands=json.dumps([bleach.clean(command, ['span','a']) for command in self.data['commands']])
        )
        scene.save()
        return [scene]

    def list(self):
        return Scene.objects.all()

    def detail(self, pk):
        scene = Scene.objects.get(pk=pk)
        return scene

    def update(self, pk):
        scene = Scene.objects.get(pk=pk)
        scene.name = self.data['name']
        commands = [bleach.clean(command, ['span','a']) for command in self.data['commands']]
        scene.commands = json.dumps(commands)
        scene.save()
        return {'status': 'ok'}

    def delete(self, pk):
        Scene.objects.get(pk=pk).delete()
        return {'status': 'ok'}

class StoryResource(Resource):
    field_mapping = {
        'scene' : 'scenes',
        'commands' : json.loads
    }
    def create(self):
        story = Story()
        story.name = self.data['name'] == '' and '[BLANK]' or self.data['name']
        story.description = self.data['description']
        story.save()
        return [story]

    def detail(self, pk):
        return Story.objects.get(pk=pk)