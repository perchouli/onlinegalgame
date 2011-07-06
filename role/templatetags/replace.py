from django import template

register = template.Library()

def replace(value):
    value = value.split(',');
    value.reverse()
    return value
    
register.filter('replace', replace)