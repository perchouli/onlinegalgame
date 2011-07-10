from diff_match_patch import diff_match_patch
from django import template
from django.utils.safestring import mark_safe
from onlinegalgame.wiki import parser, utils

register = template.Library()

@register.filter
def djiki_markup(txt):
	return mark_safe(parser.render(txt))

@register.filter
def html_diff(diff):
	html = []
	for (op, data) in diff:
		text = (data.replace("&", "&amp;").replace("<", "&lt;")\
				.replace(">", "&gt;").replace("\n", "&para;<br />"),)
		if op == diff_match_patch.DIFF_INSERT:
			html.append("<span class=\"added\">%s</span>" % text)
		elif op == diff_match_patch.DIFF_DELETE:
			html.append("<span class=\"removed\">%s</del>" % text)
		elif op == diff_match_patch.DIFF_EQUAL:
			html.append("<span>%s</span>" % text)
	return mark_safe("".join(html))

@register.filter
def urlize_title(title):
	return utils.urlize_title(title)
