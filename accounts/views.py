from django.shortcuts import redirect, render_to_response
from django.contrib.auth.decorators import login_required
from django.template.context import RequestContext
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.decorators import login_required
from django.contrib.auth import logout
from django.contrib.auth.models import User
from onlinegalgame.accounts.models import UserProfile
from onlinegalgame.accounts.forms import RegisterForm, UserProfileForm
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
	    return render_to_response('accounts/register.html',{'form' : RegisterForm()}, context_instance = RequestContext(request))

@csrf_exempt
@login_required
def profile(request, uid):
    if request.method == 'POST':
        form = UserProfileForm(request.POST)
        if form.is_valid():
            profile = UserProfile.objects.get(user=uid)
            profile.qq = form.cleaned_data['qq']
            profile.msn = form.cleaned_data['msn']
            profile.gtalk = form.cleaned_data['gtalk']
            profile.website = form.cleaned_data['website']
            profile.save()
            return redirect ('/accounts/profile/'+uid)
    else:
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
            'events'  : events,
            'form'    : UserProfileForm({'qq':profile.qq, 'msn':profile.msn, 'gtalk':profile.gtalk, 'website':profile.website})
        }
        return render_to_response('accounts/profile.html', ctx ,context_instance = RequestContext(request))

