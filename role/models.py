# -*- coding: utf-8 -*-
from django.db import models
from django.contrib.auth.models import User

class Role(models.Model):
    name = models.CharField(unique=True, max_length=32)
    birthday = models.DateField(blank=True)
    gender = models.CharField(max_length=8,blank=True)
    relation = models.CharField(max_length=16)
    author = models.ForeignKey(User)
    resume = models.TextField(blank=True)
    profile = models.TextField(blank=True)
    image = models.ImageField(upload_to='static/role/user/', blank=True)    
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
    