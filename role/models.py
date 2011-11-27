# -*- coding: utf-8 -*-
from django.db import models
from django.contrib.auth.models import User

class Role(models.Model):
    name = models.CharField(max_length=32)
    tags = models.CharField(max_length=64, blank=True, null=True)
    gender = models.CharField(max_length=8)
    relation = models.CharField(max_length=16)
    parent = models.ForeignKey('self', default=0, blank=True, null=True)
    author = models.ForeignKey(User)
    resume = models.TextField(blank=True, null=True)
    profile = models.TextField(blank=True, null=True)
    image = models.ImageField(upload_to='role/%Y/%m', blank=True, null=True)   

    def __unicode__(self):
        return u'%s' % (self.name)
    
class Dress(models.Model):
    name = models.CharField(max_length=16)
    category = models.CharField(max_length=16)
    type = models.CharField(max_length=8)
    
    def __unicode__(self):
        return u'%s' % (self.name)

class LinkRole(models.Model):
    author = models.ForeignKey(User)
    linkrole = models.ForeignKey(Role)
    token = models.CharField(unique=True, max_length=32) 