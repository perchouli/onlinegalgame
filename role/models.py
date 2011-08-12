# -*- coding: utf-8 -*-
from django.db import models
from django.contrib.auth.models import User
from django.contrib.contenttypes.models import ContentType
from django.contrib.contenttypes import generic

class Role(models.Model):
    name = models.CharField(max_length=32)
    tags = models.TextField(blank=True)
    gender = models.CharField(max_length=8,blank=True)
    relation = models.CharField(max_length=16)
    parent = models.ForeignKey('self',default=0)
    author = models.ForeignKey(User)
    resume = models.TextField(blank=True)
    profile = models.TextField(blank=True)
    image = models.ImageField(upload_to='static/role/user/%Y/%m', blank=True)   
    events = generic.GenericRelation('RoleEvent')

    def __unicode__(self):
        return u'%s %s %s' % (self.name, self.relation, self.resume)
    
class RoleDress(models.Model):
    name = models.CharField(max_length=16)
    category = models.CharField(max_length=16)
    type = models.CharField(max_length=8)
    
    def __unicode__(self):
        return u'%s' % (self.name)

class LinkRole(models.Model):
    author = models.ForeignKey(User)
    linkrole = models.ForeignKey(Role)
    token = models.CharField(unique=True, max_length=32)
 
class RoleEvent(models.Model):
    user = models.ForeignKey(User)
    content_type = models.ForeignKey(ContentType)
    object_id = models.PositiveIntegerField()
    
    event = generic.GenericForeignKey('content_type', 'object_id')
    created = models.DateTimeField(auto_now_add = True)
    
from django.db.models.signals import post_save
def role_save(sender, instance, created, *args, **kwargs):
    if created:
        event = RoleEvent(user = instance.author, event = instance)
        event.save()

post_save.connect(role_save, sender = Role)

