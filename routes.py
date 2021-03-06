# -*- coding: utf-8 -*-

import os
import time
from functools import wraps
from sqlite3 import dbapi2 as sqlite3
from flask import Flask, request, session, g, redirect, url_for, abort, \
    render_template, flash

app = Flask(__name__)

app.config.update(dict(
    DATABASE=os.path.join(app.root_path, 'tickets.db'),
    DEBUG=True,
    SECRET_KEY='supersekrit',
    PASSWORD='password'
))

def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not session.get('logged_in'):
            return redirect(url_for('login', next=request.url))
        return f(*args, **kwargs)
    return decorated_function

def connect_db():
    rv = sqlite3.connect(app.config['DATABASE'])
    rv.row_factory = sqlite3.Row
    return rv

def get_db():
    if not hasattr(g, 'sqlite_db'):
        g.sqlite_db = connect_db()
        # Cache the status table:
        g.status_table = get_statuses(g.sqlite_db)
    return g.sqlite_db

@app.teardown_appcontext
def close_db(error):
    if hasattr(g, 'sqlite_db'):
        g.sqlite_db.close()

def get_statuses(db):
    cur = db.execute("SELECT `sid`, `status` FROM `status`")
    status_table = cur.fetchall()
    return { k : v for k, v in status_table }

def search_ticket(query):
    db = get_db()
    cur = db.execute("SELECT * FROM `tickets` WHERE `ticketnumber` = ?", (query,))
    tickets = cur.fetchone()
    if tickets is None:
        return None
    tickets = dict(tickets)
    tickets['order'] = get_order(tickets['pid'])
    tickets['person'] = get_person(tickets['pid'])
    tickets['status_id'] = tickets['order']['status']
    tickets['status'] = g.status_table[tickets['order']['status']]
    tickets['seats'] = tickets['order']['seats']
    if tickets['checkin'] == 0:
        tickets['checkin'] = None
    checkin_ticket(tickets['tid'])
    return tickets

def get_person(id):
    db = get_db()
    cur = db.execute("SELECT * FROM `people` WHERE `pid` = ?", (id,))
    person = cur.fetchone()
    if person is None:
        return None
    return dict(person)

def get_order(id):
    db = get_db()
    cur = db.execute("SELECT * FROM `orders` WHERE `pid` = ?", (id,))
    order = cur.fetchone()
    if order is None:
        return None
    return dict(order)

def checkin_ticket(tid):
    db = get_db()
    db.execute("UPDATE `tickets` SET `checkin` = ? WHERE `tid` = ?",
               (int(time.time()), tid))
    db.commit()

@app.route('/')
@login_required
def index():
    return render_template('index.html')

@app.route('/scan/<code>')
@login_required
def scan(code):
    result = search_ticket(code)
    return render_template('verify.html', result=result)

@app.route('/manual', methods=['GET', 'POST'])
@login_required
def manual_scan():
    result, error = None, None
    if request.method == 'POST':
        query = request.form['input'].upper()
        query = query.replace('-', '')
        result = search_ticket(query)
        if result is None:
            error = 'No results for {0}'.format(request.form['input'])
    return render_template('manual.html', result=result, error=error)

@app.route('/unchecked')
def unchecked():
    db = get_db()
    cur = db.execute("select * from tickets join people on tickets.pid = people.pid where tickets.checkin = 0;")
    tickets = cur.fetchall()
    return render_template('unchecked.html', unchecked=tickets)

@app.route('/login', methods=['GET', 'POST'])
def login():
    error = None
    if request.method == 'POST':
        if request.form['password'] != app.config['PASSWORD']:
            error = "Access denied"
        else:
            session['logged_in'] = True
            return redirect(url_for('index'))
    return render_template('login.html', error=error)

@app.route('/logout')
def logout():
    session.pop('logged_in', None)
    return redirect(url_for('index'))

@app.template_filter('ctime')
def timectime(s):
    return time.ctime(s)

if __name__ == '__main__':
    app.run(host='0.0.0.0')
