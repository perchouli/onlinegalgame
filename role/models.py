# -*- coding: utf-8 -*-
from django.db import models
from django.contrib.auth.models import User

# Create your models here.
class UserRole(models.Model):

    name = models.CharField(unique=True, max_length=32)
    birthday = models.DateField(blank=True)
    gender = models.CharField(max_length=8)
    author = models.ForeignKey(User)
    resume = models.TextField()
    profile = models.TextField()
    
    def __unicode__(self):
        return u'%s %s %s' % (self.name, self.resume, self.resume)
    
class UserRoleDress(models.Model):
    name = models.CharField(max_length=16)
    category = models.CharField(max_length=16)
    type = models.CharField(max_length=8)
    
    def __unicode__(self):
        return u'%s' % (self.name)