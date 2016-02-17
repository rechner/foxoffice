# -*- coding: utf-8 -*-

import smtplib

import mimetypes
from email import encoders
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.mime.base import MIMEBase

from sqlite3 import dbapi2 as sqlite3

def connect_db():
    rv = sqlite3.connect('tickets.db')
    rv.row_factory = sqlite3.Row
    return rv

db = connect_db()

message_plain = """
Hello, {name}

Attached are your tickets for the Zootopia showing on March 5th @ 11:00.

Please print your ticket to present for admission.  Your tickets may also
be retrieved via telegram presented on your phone by clicking here:

https://telegram.me/novafurs_bot?start={nonce}

Event Rules
===========

The theatre will open at 11:00am.  We will be starting the showing at noon.
Alamo rules do not permit admission once a movie begins; late arrivals will
not be permitted to enter the theatre.  You will only be allowed to 
leave to go to the restroom and return.

Anyone who has not arrived by 11:55am runs the risk that your ticket will
be sold to someone waiting to get in that was unable to purchase a ticket. 
The show is non-refundable, we will not be able to refund you because you
were unable to make it.

Please do not arrive more than 30 minutes prior to the theatre opening.
We can't guarantee the building will be open.

Note that All MDFurs and NOVAfurs Code of Conduct rules apply:
  http://mdfurs.org/code-of-conduct/
  http://www.meetup.com/NOVAfurs/pages/Code_of_Conduct/

*You MUST have a ticket provided by us to get into the theatre*.  You will
be asked for your ticket by an event organiser before entering the theatre
(after the lobby).  Once scanned your ticket is no longer valid.

Please be courteous to both us and the theater and do not ghost the event.
Every seat is filled and someone standing or sitting in the aisle will be 
ejected from the building.

If you are going to wear your fursuit head DURING the movie, then you MUST
sit in the very back row. We appreciate that some people will not want to
remove their head, but there are other people in the theater as well. Most
heads will create an obstruction from viewers behind you. If you are not
sitting in the back (or second to last row in the back is full of
fursuiters), you could be asked by the theater to leave the showing.

We are guests of the theater, not of the shopping center. We are allowed to
Fursuit in the theater, and at most the sidewalk directly in front of the
theater. The property managers of ONE Loudon have forbid fursuiters from
leaving the theater property. Do NOT cross the street to derp around in the
quad, or walk around the shopping center in your fursuit. You will be asked
by property security to leave the property.

Once the movie is over, we must leave the screening room.   The theater
needs to get in as soon as were done to clean and prep the screening room
for public showings. The theater has said they don’t mind if some people
want to derp around the lobby, enjoy their bar for a little while after
the movie, perform, say hi to kids, etc.

DO NOT FURSUIT TO OTHER BUSINESSES. Once again, we DO NOT have permission
from the property owners of the shopping center to fursuit around their
center. Although it is an outdoor shopping center, it is still private
property and they have denied our request to enjoy the rest of their
property while in fursuit. We have attempted to negotiate with them, and
they are unwilling to budge. Once you leave the theater’s property which
extends only to the curb in front of the theater, you no longer have
permission to fursuit.

We will not have a headless lounge. Sorry, but lack of space, and time to
set one up is an issue. There are restrooms there which should be clean
and usable for changing.

All normal rules for theaters apply. Alamo has a strict no electronics
rule. No electronic devices should be out during the movie. If you are
seen with your phone, tablet, camera glasses, google glasses, or anything
else by an usher or waiter, they will ask you to leave without warning.

No outside food or drink. Part of your admission includes $15 towards
your food and non-alcoholic edible pleasures. Unless you have a super
special medical diet that prevents you from eating “normal” food, you
cannot bring anything in. If that is the case, talk to us before going
in so we can explain to the theater that you have a disability that
requires accommodation under ADA regulations.
"""


message_html = """
<html>
<head></head>
<body>
<p>Hello, {name}</p>

<p>Attached are your tickets for the Zootopia showing on March 5th @11:00.</p>

<p>
Please print your ticket to present for admission.  Your tickets may also
be retrieved via telegram presented on your phone by clicking 
<a href="https://telegram.me/novafurs_bot?start={nonce}">here</a>.
</p>

<div><!--[if mso]>
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:40px;v-text-anchor:middle;width:300px;" arcsize="10%" strokecolor="#1e3650" fillcolor="#556270">
<w:anchorlock/>
<center style="color:#ffffff;font-family:sans-serif;font-size:13px;font-weight:bold;">Open Tickets in Telegram</center>center>
</v:roundrect>v:roundrect>
<![endif]--><a href="https://telegram.me/novafurs_bot?start={nonce}"
style="background-color:#556270;border:1px solid #1e3650;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:300px;-webkit-text-size-adjust:none;mso-hide:all;">Open Tickets in Telegram</a></div>

<h3>Event Rules</h3>

<p>
The theatre will open at 11:00am.  We will be starting the showing at noon.
Alamo rules do not permit admission once a movie begins; late arrivals will
not be permitted to enter the theatre.  You will only be allowed to 
leave to go to the restroom and return.
</p>

<p>
Anyone who has not arrived by 11:55am runs the risk that your ticket will
be sold to someone waiting to get in that was unable to purchase a ticket. 
The show is non-refundable, we will not be able to refund you because you
were unable to make it.
</p>

<p>
Please do not arrive more than 30 minutes prior to the theatre opening.
We can't guarantee the building will be open.
</p>

<p>
Note that All <a href="http://mdfurs.org/code-of-conduct/">MDFurs</a>
and <a href="http://www.meetup.com/NOVAfurs/pages/Code_of_Conduct/">NOVAfurs</a> 
Code of Conduct rules apply:
</p>

<p>
<b>You MUST have a ticket provided by us to get into the theatre</b>.  You will
be asked for your ticket by an event organiser before entering the theatre
(after the lobby).  Once scanned your ticket is no longer valid.<br><br>

Please be courteous to both us and the theater and do not ghost the event.
Every seat is filled and someone standing or sitting in the aisle will be 
ejected from the building.
</p>

<p>
<b>If you are going to wear your fursuit head DURING the movie, then you MUST
sit in the very back row.</b> We appreciate that some people will not want to
remove their head, but there are other people in the theater as well. Most
heads will create an obstruction from viewers behind you. If you are not
sitting in the back (or second to last row in the back is full of
fursuiters), you could be asked by the theater to leave the showing.
</p>

<p>
<b>We are guests of the theater, not of the shopping center.</b> We are allowed to
Fursuit in the theater, and at most the sidewalk directly in front of the
theater. The property managers of ONE Loudon have forbid fursuiters from
leaving the theater property. Do NOT cross the street to derp around in the
quad, or walk around the shopping center in your fursuit. You will be asked
by property security to leave the property.
</p>

<p>
<b>Once the movie is over, we must leave the screening room.</b>  The theater
needs to get in as soon as were done to clean and prep the screening room
for public showings. The theater has said they don’t mind if some people
want to derp around the lobby, enjoy their bar for a little while after
the movie, perform, say hi to kids, etc.
</p>

<p>
<b>DO NOT FURSUIT TO OTHER BUSINESSES.</b> Once again, we DO NOT have permission
from the property owners of the shopping center to fursuit around their
center. Although it is an outdoor shopping center, it is still private
property and they have denied our request to enjoy the rest of their
property while in fursuit. We have attempted to negotiate with them, and
they are unwilling to budge. Once you leave the theater’s property which
extends only to the curb in front of the theater, you no longer have
permission to fursuit.
</p>

<p>
<b>We will not have a headless lounge.</b> Sorry, but lack of space, and time to
set one up is an issue. There are restrooms there which should be clean
and usable for changing.
</p>

<p>
<b>All normal rules for theaters apply.</b> Alamo has a strict no electronics
rule. No electronic devices should be out during the movie. If you are
seen with your phone, tablet, camera glasses, google glasses, or anything
else by an usher or waiter, they will ask you to leave without warning.
</p>

<p>
<b>No outside food or drink.</b> Part of your admission includes $15 towards
your food and non-alcoholic edible pleasures. Unless you have a super
special medical diet that prevents you from eating “normal” food, you
cannot bring anything in. If that is the case, talk to us before going
in so we can explain to the theater that you have a disability that
requires accommodation under ADA regulations.
</p>

</body>
</html>
"""

def get_person(db, pid):
    cur = db.execute("SELECT * FROM `people` WHERE `pid` = ?", (pid,))
    person = cur.fetchone()
    if person is None:
        return None
    return dict(person)

def get_telegram_hash(db, pid):
    cur = db.execute("SELECT `hash` FROM `telegram_auth` WHERE `pid` = ?", (pid,))
    hash_str = cur.fetchone()
    if hash_str is None:
        return None
    return dict(hash_str)['hash']


cur = db.execute("SELECT * FROM orders WHERE emailed = 0")
orders = cur.fetchall()

server = smtplib.SMTP('smtp.gmail.com:587')
server.ehlo()
server.starttls()

server.login('novafurs@someplace.net', 'PASSWORD')

for order in orders:
    order = dict(order)
      
    person = get_person(db, order['pid'])
    ticket_pdf_path = 'webroot/pdf/{0}.pdf'.format(order['filename'])
    
    email = person['email']
    print("Emailing {0}...".format(email))
    email = "jkltechinc@gmail.com"

    msg = MIMEMultipart('alternative')
    msg['Subject'] = "Zootopia Showing Tickets"
    msg['From'] = "do-not-reply@novafurs.com"
    msg['To'] = email

    telegram_hash = get_telegram_hash(db, order['pid'])

    plain_part = MIMEText(message_plain.format(name=person['name'], nonce=telegram_hash), 'plain')
    html_part = MIMEText(message_html.format(name=person['name'], nonce=telegram_hash), 'html')

    msg.attach(plain_part)
    msg.attach(html_part)

    ctype, encoding = mimetypes.guess_type(ticket_pdf_path)
    if ctype is None or encoding is None:
        ctype = 'application/octet-stream'
    maintype, subtype = ctype.split('/', 1)
    fp = open(ticket_pdf_path, 'rb')
    pdf_part = MIMEBase(maintype, subtype)
    pdf_part.set_payload(fp.read())
    fp.close()
    encoders.encode_base64(pdf_part)
    pdf_part.add_header('Content-Disposition', 'attachment', 
        filename="ZootopiaTicket_{0}.pdf".format(order['filename']))

    msg.attach(pdf_part)

    server.sendmail(msg['From'], email, msg.as_string())
    
server.quit()
    

