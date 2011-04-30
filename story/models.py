# -*- coding: utf-8 -*-
from django.db import models
from django.contrib.auth.models import User

# Create your models here.
class UserStory(models.Model):

    title = models.CharField(unique=True, max_length=32)
    cdate = models.DateField(blank=True)
    author = models.ForeignKey(User)
    summary = models.TextField()
    process = models.TextField()
    
    def __unicode__(self):
        return u'%s %s %s' % (self.title, self.summary, self.process)
    
