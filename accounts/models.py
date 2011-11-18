from django.db import models 
from django.contrib.auth.models import User
from django.contrib.contenttypes.models import ContentType
from django.contrib.contenttypes import generic
from userena.models import UserenaLanguageBaseProfile
from django.utils.translation import ugettext_lazy as _

class UserProfile(UserenaLanguageBaseProfile):
    user = models.OneToOneField(User,
                                unique=True,
                                verbose_name=_('user'),
                                related_name='olgg_profile')

    
    qq = models.CharField(max_length=16,blank=True)
    msn = models.CharField(max_length=32,blank=True)
    gtalk = models.CharField(max_length=32,blank=True)
    website = models.CharField(max_length=32,blank=True)
    
    honor = models.CharField(max_length=32,blank=True)
    career = models.CharField(max_length=16,blank=True)

'''  
class UserProfileEvent(models.Model):
    user = models.ForeignKey(User)
    content_type = models.ForeignKey(ContentType)
    object_id = models.PositiveIntegerField()
    
    event = generic.GenericForeignKey('content_type', 'object_id')
    created = models.DateTimeField(auto_now_add = True)
  
from django.db.models.signals import post_save
def user_profile_save(sender, instance, created, *args, **kwargs):
    if created:
        event = UserProfileEvent(user = instance.user, event = instance)
        event.save()

post_save.connect(user_profile_save, sender = UserProfile)
'''