from django.shortcuts import redirect, render_to_response
from django.contrib.auth.decorators import login_required
from django.template.context import RequestContext
from django.contrib.auth import logout
from django.contrib.auth.models import User

def register(request):
    if request.method == 'POST':
        form = request.POST
        user = User.objects.create_user(
            form['username'],
            form['email'],
            form['password'],
            )
        user.is_staff = True
        user.save()
        return redirect ('/accounts/profile')
    else:
#    form = request
	    return render_to_response('accounts/register.html',
    #{'form':form ,'my':'test'}, 
    context_instance = RequestContext(request))

@login_required
def profile(request):
    user = User.objects.get(username='admin')
    profile = { 
    'sex':user.get_profile().sex,
    'ai' : 10
    }
    #profile['ai']  = 10
    return render_to_response('accounts/profile.html', {'profile':profile} ,context_instance = RequestContext(request))


@login_required
def friends(request):
	return render_to_response('accounts/friends.html',context_instance = RequestContext(request))


@login_required
def olgg_logout(request):
	logout(request)
	#return render_to_response('index.html',context_instance = RequestContext(request))
	return redirect('/accounts/login')