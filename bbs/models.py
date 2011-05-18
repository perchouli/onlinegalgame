from django.db import models
from django.contrib.auth.models import User

class Threads(models.Model):
    author = models.OneToOneField(User)
    title = models.CharField(max_length=64)
    context = models.TextField()
    
    def __unicode__(self):
        return u'%s %s' % (self.title, self.context)
    
class Replys(models.Model):
    thread = models.OneToOneField(Threads)
    author = models.OneToOneField(User)
    context = models.TextField()
    
    def __unicode__(self):
        return u'%s %s' % (self.title, self.context)