from django.db import models
from django.contrib.auth.models import User
from django.db.models.signals import post_save


class Profile(models.Model):
    user = models.OneToOneField(User)
    sex = models.CharField(max_length=15)

    def __unicode__(self):
        return self.user.username


def save_profile(sender, **kwargs):
    if kwargs['created'] == True:
        Profile.objects.create(user=kwargs['instance'])
        
post_save.connect(receiver = save_profile, sender = User, weak = False)
