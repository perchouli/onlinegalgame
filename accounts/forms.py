from django import forms

class RegisterForm(forms.Form):
    username = forms.CharField(max_length=16)
    password = forms.CharField(min_length=6, widget=forms.PasswordInput )
    email = forms.EmailField()
