# -*- coding: utf-8 -*-
from django.db import models
from django.contrib.auth.models import User

from django.contrib.contenttypes.models import ContentType
from django.contrib.contenttypes import generic
# Create your models here.
class UserStory(models.Model):

    title = models.CharField(max_length=32)
    cdate = models.DateField(blank=True)
    author = models.ForeignKey(User)
    summary = models.TextField(blank=True)
    process = models.TextField()
    sort = models.IntegerField(default=0)
    image = models.ImageField(upload_to='static/story/%Y/%m', blank=True)
    events = generic.GenericRelation('StoryEvent')
    
    def __unicode__(self):
        return u'%s ' % (self.title)
    
class StoryEvent(models.Model):
    user = models.ForeignKey(User)
    content_type = models.ForeignKey(ContentType)
    object_id = models.PositiveIntegerField()
    
    event = generic.GenericForeignKey('content_type', 'object_id')
    created = models.DateTimeField(auto_now_add = True)
    
from django.db.models.signals import post_save
def story_save(sender, instance, created, *args, **kwargs):
    if created:
        event = StoryEvent(user = instance.author, event = instance)
        event.save()

post_save.connect(story_save, sender = UserStory)
