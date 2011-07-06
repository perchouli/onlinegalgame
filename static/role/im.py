#!/usr/bin/env python

import Image
import ImageDraw

im = Image.new('RGBA', (267,400))
draw = ImageDraw.Draw(im)
p = Image.open('2.png')
a = Image.open('hair_0_0.gif')
b = Image.open('cloth_0_0.gif')
c = Image.open('eye_0_0.png')
b.paste(a, (0,0),a)
#b.paste(c, (0,0),c)
#im.paste(b, (0,0,267,400),a)
#b.save('1.png','PNG')
#print a.format, a.mode
b.show()
