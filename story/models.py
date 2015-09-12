from django.contrib.auth.models import User
from django.db import models

class Story(models.Model):
    name = models.CharField(max_length=128)
    description = models.TextField(blank=True)

    def scenes(self):
        return []

class Background(models.Model):
    user = models.ForeignKey(User, related_name='backgrounds')
    name = models.CharField(max_length=128)
    image = models.ImageField(upload_to='bgd/%Y%m', blank=True)
    created_at = models.DateTimeField(auto_now_add=True, null=True)
    def __unicode__(self):
        return self.name

class Role(models.Model):
    user = models.ForeignKey(User, related_name='roles')
    name = models.CharField(max_length=128)
    image = models.ImageField(upload_to='roles/%Y%m', blank=True)
    created_at = models.DateTimeField(auto_now_add=True, null=True)
    def __unicode__(self):
        return self.name


class Scene(models.Model):
    story = models.ForeignKey(Story, null=True, blank=True)
    name = models.CharField(max_length=128, unique=False)
    commands = models.TextField(blank=True)

    class Meta:
        unique_together = (('name', ),)

    def natural_key(self):
        return (self.name,) + self.bgd.name