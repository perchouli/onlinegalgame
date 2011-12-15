#coding=utf-8
from django import forms
from django.forms import ModelForm
from django.contrib.auth.models import User
from accounts.models import UserProfile

class RegisterForm(forms.Form):
    username = forms.CharField(max_length=16)
    password = forms.CharField(min_length=6, max_length = 16,
                               error_messages = {'required': u'密码不能为空',
                                                       'min_length': u'密码长度至少为6位',
                                                       'max_length': u'密码长度最大为16位'},
                                                    widget=forms.PasswordInput(render_value = False,
                               attrs = {'class': 'input-txt1'}) )
    email = forms.EmailField(max_length = 75,
                            widget = forms.TextInput(attrs = {'class': 'input-txt1'}),
                             error_messages = {'required': u'邮箱不能为空',
                                                         'invalid': u'邮箱格式不正确',
                                                         'max_length':u'邮箱的最大长度为75位'})

    def clean_username(self):
        '''
        '''
        value = self.cleaned_data['username']      
        user = User.objects.filter(username=value)
        if user:
            raise forms.ValidationError(u'该用户名已存在，请重新输入')
        return value
    
    def clean_email(self):
        '''
        '''
        value = self.cleaned_data['email']
        email = User.objects.filter(email = value)
        if email:
            raise forms.ValidationError(u'该邮箱已存在，请重新输入')
        return value
    
class UserProfileForm(ModelForm):
    class Meta:
        model = UserProfile
        exclude = ('user', 'honor', 'career')