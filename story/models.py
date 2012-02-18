# -*- coding: utf-8 -*-
from django.db import models
from django.contrib.auth.models import User

# Create your models here.
class UserStory(models.Model):

    title = models.CharField(max_length=32)
    cdate = models.DateField(blank=True)
    author = models.ForeignKey(User)
    summary = models.TextField(blank=True)
    process = models.TextField()
    sort = models.IntegerField(default=0)
    image = models.ImageField(upload_to='media/story/%Y/%m', blank=True)
    
    def __unicode__(self):
        return u'%s ' % (self.title)
    
