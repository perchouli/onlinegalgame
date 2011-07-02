#-*- coding:utf-8 -*-
from django import forms

class RoleForm(forms.Form):
    role_image  = forms.FileField()