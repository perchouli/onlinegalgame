#!/usr/bin/env python
import Image

image = Image.new ('RGBA', (267, 400) )


a = Image.open('static/role/hair_0_0.png')
transparency = a.info['transparency']

b = Image.open('static/role/cloth_0_0.png')

transparency = b.info['transparency']
im2 = Image.open('static/role/face_0_0.gif')
#im3 = Image.open('static/role/eye_0_0.gif')
#images = Image.blend(im1, im3, 0.5 )
#region = im1.crop(box)
#region = region.transpose(Image.ROTATE_180)
alpha = b.splite()[3]
bgmask = alpha.point(lambda x: 255-x)
a.paste(b,None,bgmask)
a.show()
#im = ImageChops.multiply(im1,im2)
#im1.save('1.png',transparency=transparency)
#a.save('1.gif')
