from django import template

register = template.Library()

def islinkuser(value, link_role_list):
    if value in link_role_list:
        return True
    else:
        return False
    
register.filter('islinkuser', islinkuser)