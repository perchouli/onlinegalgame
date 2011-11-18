from role.models import Role, RoleEvent
from django.db.models.signals import post_save
def role_save(sender, instance, created, *args, **kwargs):
    if created:
        event = RoleEvent(user = instance.author, event = instance)
        event.save()

post_save.connect(role_save, sender = Role)

