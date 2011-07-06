from django.db import models
from django.contrib.auth.models import User

class StoryUpload(models.Model):
    uid = models.ForeignKey(User)
    image = models.CharField(max_length=128)#ImageField(upload_to='/static/',blank=True)
# Create your models here.
