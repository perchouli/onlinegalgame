from django.db import models
from django.contrib.auth.models import User

class Threads(models.Model):
    author = models.ForeignKey(User)
    title = models.CharField(max_length=64)
    content = models.TextField()
    
    def __unicode__(self):
        return u'%s %s' % (self.title, self.content)
    
class Replys(models.Model):
    thread = models.ForeignKey(Threads)
    author = models.ForeignKey(User)
    content = models.TextField()
    
    def __unicode__(self):
        return u'%s %s' % (self.title, self.content)