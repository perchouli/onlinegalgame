#-*- coding:utf-8 -*-
from django import forms
from django.utils.translation import ugettext_lazy as _

class RegisterForm(forms.Form):
    username = forms.CharField(label= _('Username'),max_length=16)
    password = forms.CharField(label= u'密码', min_length=6, widget=forms.PasswordInput )
    email = forms.EmailField(label= u'邮箱地址')

