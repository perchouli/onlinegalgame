from django.contrib import admin

from .models import Background, Role, Scene, Story

#class BackgroundAdmin(admin.ModelAdmin):
#    list_display = ('name', 'user', 'image', 'created_at')

admin.site.register(Background)
admin.site.register(Role)
admin.site.register(Scene)
admin.site.register(Story)