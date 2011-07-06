#-*- coding:utf-8 -*-
from django import forms
from onlinegalgame.role.models import Role
class RoleForm(forms.Form):
    name = forms.CharField()
#    class Meta:
#        model = Role
    #role_image  = forms.FileField()