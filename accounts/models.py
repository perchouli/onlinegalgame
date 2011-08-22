from django.db import models
from django.contrib.auth.models import User

ZODIAC_CHOICES = ()

class UserProfile(models.Model):
    user = models.ForeignKey(User)
    
    qq = models.CharField(max_length=16,blank=True)
    msn = models.CharField(max_length=32,blank=True)
    gtalk = models.CharField(max_length=32,blank=True)
    website = models.CharField(max_length=32,blank=True)
    
    honor = models.CharField(max_length=32,blank=True)
    career = models.CharField(max_length=16,blank=True)
    