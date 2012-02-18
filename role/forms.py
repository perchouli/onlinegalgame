#-*- coding:utf-8 -*-
from django import forms
from django.utils.translation import ugettext as _

from .models import Role

class RoleForm(forms.ModelForm):
    image  = forms.ImageField(required=False)
    
    class Meta:
        model = Role
        fields = ('name', 'tags', 'relation', 'resume')
        