from django import forms
from django.forms import ModelForm
from accounts.models import UserProfile

class RegisterForm(forms.Form):
    username = forms.CharField(max_length=16)
    password = forms.CharField(min_length=6, widget=forms.PasswordInput )
    email = forms.EmailField()

class UserProfileForm(ModelForm):
    class Meta:
        model = UserProfile
        exclude = ('user', 'honor', 'career')