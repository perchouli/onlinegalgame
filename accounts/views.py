from django.shortcuts import redirect, render_to_response
from django.contrib.auth.decorators import login_required
from django.template.context import RequestContext
from django.contrib.auth import logout
from django.contrib.auth.models import User
from onlinegalgame.accounts.models import UserProfile
from onlinegalgame.accounts.forms import RegisterForm
from onlinegalgame.role.models import Role, RoleEvent
from onlinegalgame.story.models import UserStory, StoryEvent

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
    else:
	    return render_to_response('accounts/register.html',{'form' : RegisterForm() }, context_instance = RequestContext(request))

@login_required
def profile(request,uid):
    # Unfinished
    user = User.objects.get(id=uid)
    try:
        profile = user.get_profile()
    except:
        #profile = UserProfile.objects.create(user=user,qq='',msn='',gtalk='',website='')
        profile = UserProfile(user=user,qq='',msn='',gtalk='',website='')
        profile.save()
    roles = Role.objects.filter(author=uid).filter(parent=0).order_by('-id')[0:5]
    stories = UserStory.objects.filter(author=uid).order_by('sort')[0:5]
    events = list(RoleEvent.objects.filter(user=uid)) + list(StoryEvent.objects.filter(user=uid))
    
    ctx = {
        'user'  : user,
        'roles' : roles,
        'stories' : stories,
        'profile' : profile,
        'events'  : events
    }
    return render_to_response('accounts/profile.html', ctx ,context_instance = RequestContext(request))

