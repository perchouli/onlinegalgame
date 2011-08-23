from django import template
import urllib, hashlib

register = template.Library()
 
class GravatarUrlNode(template.Node):
    def __init__(self, email):
        self.email = template.Variable(email)

    def render(self, context):
        try:
            email = self.email.resolve(context)
        except template.VariableDoesNotExist:
            return ''
        default = "http://onlinegalgame.com/static/images/noavatar.jpg"
        size = 120
        gravatar_url = "http://www.gravatar.com/avatar/" + hashlib.md5(email.lower()).hexdigest() + "?"
        gravatar_url += urllib.urlencode({'d':default, 's':str(size)})
        return gravatar_url
 
@register.tag
def gravatar_url(parser, token):
    try:
        tag_name, email = token.split_contents()
    except ValueError:
        raise template.TemplateSyntaxError, "%r tag requires a single argument" % token.contents.split()[0]
    return GravatarUrlNode(email)
