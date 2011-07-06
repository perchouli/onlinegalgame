from django.shortcuts import redirect, render_to_response
from django.contrib.auth.decorators import login_required
from django.template.context import RequestContext
from django.contrib.auth import logout
from django.contrib.auth.models import User
from onlinegalgame.accounts.forms import RegisterForm

def register(request):
    if request.method == 'POST':
        form = RegisterForm(request.POST)
        if form.is_valid():
            user = User.objects.create_user(
                form.cleaned_data['username'],
                form.cleaned_data['email'],
                form.cleaned_data['password'],
            )
            user.is_staff = True
            user.save()
            return redirect ('/accounts/login/?next=/')
        else:
            error_form = RegisterForm(initial={
                'username':request.POST['username'],
                'email':request.POST['email']
            })
            ctx = {
                'form'  : error_form,
                'error' : form.errors,
            }
            return render_to_response('accounts/register.html', ctx , context_instance = RequestContext(request))
            #return form.errors
    else:
	    return render_to_response('accounts/register.html',{'form' : RegisterForm() }, context_instance = RequestContext(request))

@login_required
def profile(request):
    
    user = User.objects.get(username='admin')
    
    import urllib, hashlib
    default =  "http://en.gravatar.com/js/SyntaxHighlighter/styles/help.png"
    email = user.email
    size = 100
    
    gravatar_url = "http://www.gravatar.com/avatar/" + hashlib.md5(email.lower()).hexdigest() + "?"
    gravatar_url += urllib.urlencode({'d':default, 's':str(size)})
    
    
    profile = { 
    'sex':user.get_profile().sex,
    'ai' : email,
    'avatar' : gravatar_url,
    }
    #profile['ai']  = 10
    return render_to_response('accounts/profile.html', {'profile':profile} ,context_instance = RequestContext(request))


@login_required
def friends(request):
	return render_to_response('accounts/friends.html',context_instance = RequestContext(request))

@login_required
def olgg_logout(request):
	logout(request)
	return redirect('/accounts/login')
