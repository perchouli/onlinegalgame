from django.views.decorators.csrf import csrf_exempt
from django.core import serializers
from django.conf.urls import patterns, url
from django.forms.models import model_to_dict
from django.db.models.fields.files import ImageFieldFile
from django.db.models.query import QuerySet
#from django.db.models.fields.related import ManyToO

import datetime
import decimal
import traceback
import json

class MoreTypesJSONEncoder(json.JSONEncoder):

    def default(self, data):
        if isinstance(data, (datetime.datetime, datetime.date, datetime.time)):
            return data.isoformat()
        elif isinstance(data, decimal.Decimal):
            return str(data)
        elif isinstance(data, ImageFieldFile):
            return data.url
        else:
            return super(MoreTypesJSONEncoder, self).default(data)

class JSONSerializer:
    def deserialize(self, body):
        if isinstance(body, bytes):
            return json.loads(body.decode('utf-8'))
        return json.loads(body)

    def serialize(self, data):
        return json.dumps(data, cls=MoreTypesJSONEncoder)



class Resource(object):
    http_methods = {
        'list': {
            'GET': 'list',
            'POST': 'create',
            'PUT': 'update_list',
            'DELETE': 'delete_list',
        },
        'detail': {
            'GET': 'detail',
            'POST': 'create_detail',
            'PUT': 'update',
            'DELETE': 'delete',
        }
    }
    serializer = JSONSerializer()

    def request_body(self):
        return self.request.body #Django

    def build_response(self, data, status=200):
        from django.http import HttpResponse
        response = HttpResponse(data, content_type='application/json')
        response.status_code = status
        return response

    def obj_to_dict(self, obj):
        if type(obj) == dict: return obj
        data = model_to_dict(obj)

        for related_object in obj._meta.get_all_related_objects():
            data[related_object.name] = [self.obj_to_dict(_v) for _v in getattr(obj, related_object.name + '_set').all()]

        if hasattr(self, 'field_mapping'):
            for k, v in self.field_mapping.items():
                if data.has_key(k):
                    field_result = self.field_mapping[k]
                    if type(field_result) == str:
                        data[field_result] = data[k]
                        del data[k]
                    else:
                        data[k] = field_result(data[k])

        return data

    def deserialize(self, method, endpoint, body):
        if endpoint == 'list':
            if not body:
                return []
            return self.serializer.deserialize(body)

        else:
            if not body:
                return {}
            return self.serializer.deserialize(body)

    def serialize(self, method, endpoint, data):
        if endpoint == 'list':
            data = [self.obj_to_dict(row) for row in data]
            return self.serializer.serialize(data)
        return self.serializer.serialize(self.obj_to_dict(data))

    def handle(self, endpoint, *args, **kwargs):
        self.endpoint = endpoint
        method = self.request.method.upper()


        self.data = self.deserialize(method, endpoint, self.request_body())
        view_method = getattr(self, self.http_methods[endpoint][method])
        data = view_method(*args, **kwargs)
        serialized = self.serialize(method, endpoint, data)


        return self.build_response(serialized)

    @classmethod
    def as_list(cls, *init_args, **init_kwargs):
        def _wrapper(request, *args, **kwargs):
            inst = cls(*init_args, **init_kwargs)
            inst.request = request
            return inst.handle('list', *args, **kwargs)
        return csrf_exempt(_wrapper)

    @classmethod
    def as_detail(cls, *init_args, **init_kwargs):
        def _wrapper(request, *args, **kwargs):
            inst = cls(*init_args, **init_kwargs)
            inst.request = request
            return inst.handle('detail', *args, **kwargs)
        return csrf_exempt(_wrapper)

    @classmethod
    def urls(cls, name_prefix=None):
        return patterns('',
            url(r'^$', cls.as_list()),
            url(r'^(?P<pk>\d+)/$', cls.as_detail()),
        )

