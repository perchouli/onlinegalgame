#!/usr/bin/env python

import Image
import ImageDraw

im = Image.new('RGBA', (267,400))
draw = ImageDraw.Draw(im)

a = Image.open('../static/role/hair_0_0.png')
b = Image.open('../static/role/cloth_0_0.png')
c = Image.open('../static/role/eye_0_0.png')
b.paste(a, (0,0),a)
#b.paste(c, (0,0),c)
#im.paste(b, (0,0,267,400),a)
#b.save('1.png','PNG')
#print a.format, a.mode
b.show()
