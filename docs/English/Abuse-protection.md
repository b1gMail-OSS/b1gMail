# Abuse protection

The abuse protection hands out points when a user behaves suspiciously, for example tries to send more mail than the group allows. When the sum of the points of an account reaches the _lock limit_, b1gMail can react: do nothing, lock the account or move it to a group of its own. This page describes how points are given, how to set up the reaction and how to release an account again.

## How it works

1. A violation, for example a rejected send in the webmail, gives the user points (section "How points are given").
2. Points expire after the configured time. Only points that have not expired count.
3. On **every run of `cron.php`**, b1gMail checks whether the points of an account reach the _lock limit_ and carries out the configured reaction. Without a cron job nothing happens, even though points are given. The expiry of points is checked once an hour.
4. Optionally the admin gets an email. The affected accounts are listed in the admin area under _Users & Groups_ > _Abuse protection_.

## How points are given

Points are only given to users in groups that have the option _Abuse protection_ switched on (_Users & Groups_ > _Groups_, edit the group). Check the option for every group that is to be protected. You set the points per violation type under _Preferences_ > _Abuse protection_.

| Violation type | Trigger | Default |
|---|---|---|
| Attempt to exceed the recipient count limit | Webmail: more recipients in one mail than the _Maximum number of recipients_ of the group | 5 |
| Attempt to exceed the sending frequency limit | Webmail: more recipients than the _Send limit_ of the group allows, for example 10 recipients in 1440 minutes | 25 |
| Attempt to send to blocked recipients | Webmail: the recipient is in the list _Forbidden recipients_ | 15 |
| Sending of an email too quickly after opening the compose form | Webmail: the form was sent faster than the configured time (5 seconds by default), typical for scripts | 20 |
| High email receiving frequency | **Receiving**: more than _Max. amount_ mails (default 50) in _Period (minutes)_ (default 5). Points for **every** further mail, to the recipient (see pitfalls) | 5 |
| High incoming email traffic | **Receiving**: more than _Max. traffic (MB)_ (default 100) in _Period (minutes)_ (default 5). Points for **every** further mail, to the recipient (see pitfalls) | 5 |
| Attempt to send to existing domain, but non-existing recipient; attempt to send to non-existing domain; SMTP submission without prior POP3/IMAP login | The core of b1gMail does not trigger these three types itself. Whether they give points in your installation depends on the mail server or a plugin. | 10 / 10 / 20 |

Pitfalls:

* **Send limit and webmail:** The _Send limit_ and the sending types above apply to the compose form in the webmail. The core of b1gMail does not count mails that a mail program submits over SMTP. Against an abused SMTP access, use the group option _SMTP_ (see target group below).
* **Receiving gives the recipient points, for every single mail:** If a mailbox has received more mails (or more data) than allowed in the configured period, **every further** incoming mail gives the points of the type, not only the first. With 5 points and 500 mails within five minutes that is 450 mails over the threshold and so 2250 points, many times any usual _lock limit_. Whoever is the victim of a mail flood is locked or moved this way. For these two types the abuse protection cannot tell victim and offender apart, and a mailbox with many legitimate mails (mailing list, newsletter) reaches the threshold as well.
  * **Remedy:** Set the points of both receiving types (_High email receiving frequency_ and _High incoming email traffic_) to **0** and save the page. A mail flood then gives no points any more. b1gMail still writes an entry with 0 points for each mail.
  * Raising the thresholds (_Max. amount_, _Max. traffic_) is not enough: whoever exceeds them gets points for every mail again.
  * There is no option for only these two types. The group option _Abuse protection_ switches off all types at once.
* **Points have to be saved:** For a violation type without a saved value, b1gMail gives 0 points. On an installation without example data this only changes after you have saved _Preferences_ > _Abuse protection_ once with _Save_. Before that, the page shows the default values, which is not the same as saved.
* **The _lock limit_ has to be higher than the points of a single type**, otherwise one violation is enough. With 25 points per sending violation, 100 is a good value (four violations). The settings page warns if the limit is too low.

## Setting up in six steps

1. **Create the target group** (_Users & Groups_ > _Groups_), for example `Spammer`. A group of its own, not the default group of the registration and not a group customers can switch to otherwise. Recommended settings:
   * Switch _SMTP_ off: the mail server then accepts no more mail of this account over SMTP.
   * Set the _Send limit_ to one email in 1440 minutes: this limits sending in the webmail. _SMTP_ alone does not stop it.
   * Leave login, receiving and storage as they are, so the user sees the warning and does not lose the mailbox.
2. **Check and save the points** (_Preferences_ > _Abuse protection_, section _Point types_): check the values, set the two receiving types to **0** (see pitfalls, otherwise victims of a mail flood are moved), **save the page once**.
3. **Set the limits:** _Warn limit_ (for example 50, colours the display in the overview yellow and appears on the start page of the admin area) and _Lock limit_ (for example 100). _Points expire after_: 24 to 72 hours. With 0 hours the points expire at the next hourly run. The _Expiration plan_: _All points expire as soon as the user did not get any new points in the specified timeframe_ (a permanently suspicious account keeps its points) or _Points expire individually as soon as they reach the specified age_.
4. **Choose the reaction:** _When the lock limit is reached_ > _Move account to a group_ and select the _Target group_ from step 1. Without a target group the choice silently falls back to _Do nothing_ when you save.
5. **Switch on the notification** (_Notification_ > _Notify admin / support team by email_, with _Email address_). Otherwise you only learn about a move by looking at the overview.
6. **Check the cron job:** `cron.php` has to run every minute (see the page about the cron job). The _Last cron run_ under _Preferences_ > _Common_ is green.

The feature is off by default.

## Settings

Under _Preferences_ > _Abuse protection_:

| Setting | Meaning |
|---|---|
| _Warn limit_ | From this number of points the account is yellow in the overview, and the start page of the admin area names the number of accounts above it. It does not trigger a reaction. |
| _Lock limit_ | From this number of points the reaction is triggered. |
| _Points expire after_ | Lifetime of the points in hours. |
| _Expiration plan_ | See step 3. |
| _When the lock limit is reached_ | Exactly one of three options: _Do nothing_ (the points are only collected), _Lock account_ (the user cannot log in any more) or _Move account to a group_. |
| _Target group_ | The group abused accounts are moved to. It is used for the automatic move and for the button on the detail page of the user. The dropdown is only active when _Move account to a group_ is selected. The saved group is kept when you select something else afterwards. |
| _Delete outgoing emails from the mail queue_ | Only shown when the b1gMailServer is installed and active, and only selectable when _Move account to a group_ is selected. When an account is moved (automatic move, button and mass action in the overview), its waiting **outgoing** emails are deleted from the queue of the mail server, so that the spam is not delivered after the move. Incoming emails are kept. Emails that have already been delivered cannot be recalled. Deleting cannot be undone. Off by default. |
| _Notification_ | An email to the admin or support team when an account has been locked or moved automatically (_Notify admin / support team by email_, plus the _Email address_). It contains the points and their types, a link to the detail page of the user and, for a move, a section about the mail queue: how many outgoing emails were deleted (or that it was not cleared) and for each deleted email the recipient, the subject and the last known status of the mail server (up to 50). |

You change the texts of the notifications under _Preferences_ > _Languages_, tab _Customizable texts_: _Abuse protect move email subject_ and _Abuse protect move email text_ (and for locking _Abuse protect lock email subject_ and _Abuse protect lock email text_). The text for a move contains the placeholder `%%queue%%` for the section about the mail queue. If you have changed the texts before, add it yourself.

## Why move instead of lock?

A locked user cannot log in and never learns what is going on. For abused accounts whose password has been stolen that is unfortunate: the customer only notices that nothing works any more. If you move the account to a group that cannot send anything any more, the user can still log in. The user then sees a warning on every page that cannot be closed: the account is being abused, the password has to be changed at once.

**Important:** Moving does not invalidate the stolen password, and open sessions and logged-in mail programs keep working as far as the group allows them. The abuse only ends when the password is changed. Ask the user to do so, or set a new one yourself on the edit page of the user (_(New) password_).

## Moving with one click

If a target group is set, the detail page of every user in the abuse protection (_Users & Groups_ > _Abuse protection_, click the user) has a button _Move to group "Spammer"_ next to _Lock_. This works whether automatic moving is on or not. If no target group is set, or the group that is set does not exist any more, there is a note with a link to the settings instead. The button is also missing when the user is already in the target group.

In the overview, the mass action _Move_ lists all groups. If you pick the target group there, the account is moved in the same way as with the button, with note, log entry and, if set, the mail queue.

If you move an account into the target group in another way (mass action in the normal user list, edit page of the user), only the group is changed, without note, log entry and without clearing the mail queue. Use the overview of the abuse protection for that.

## What happens when an account is moved

* The group of the user is changed. The user stays unlocked.
* A line with date, time, admin (or the note that the abuse protection did it by itself), the target group, the points, the lock limit and the **previous group** is appended to the _notes_ of the user. This lets you restore the account later.
* A system log entry is written.
* If the mail queue option is on, the waiting outgoing emails of the user are deleted and the number is noted in the system log. If b1gMail cannot reach the mail server, only the queue table is changed and the mail server does not receive the delete command. Check the queue in the mail server itself in that case.
* The points are kept. They document the incident and expire according to _Expiration plan_ and _Points expire after_.
* The automatic move does not touch accounts that are already in the target group or that are locked, nor accounts in groups where _Abuse protection_ is switched off.

## Releasing an account

1. Read the _notes_ of the user (edit page). The **previous group** is noted there.
2. Move the account back: in the overview of the abuse protection tick the user and run the mass action _Move_ > _Move to "previous group"_, or change the group on the edit page of the user, or use the mass action _Move_ in the user list.
3. The points of the user expire, as with _Unlock_, and a line with date, time, admin and the points is appended to the _notes_. Otherwise the automatic move would move the user back with its next run, because the user still reaches the _lock limit_. This only applies to users who were in the target group.
4. Check that the user has changed the password (see above). Otherwise the user will soon be suspicious again.

_Unlock_ (overview or detail page) also resets the points and lifts a lock, but does not change the group.

## Warning for the user

Users in the target group see a red notice with a button _Change password_ at the top of the pages of the user area. The notice cannot be closed. You change the text under _Preferences_ > _Languages_, tab _Customizable texts_ (_Abuse warning for users in the target group_). The first line is the headline, the rest is the text below it. If the text is empty, b1gMail shows the default text of the language of the user, the warning does not disappear.

The warning is part of the templates `modern` and `tabler` (`li/index.tpl`). The look in the `modern` template is in `style/loggedin.css` (class `.bm-abuse-warning`). A template of your own needs the same block from the `index.tpl` of these templates, otherwise the user does not see a warning.

## Update

The feature needs three new columns in the settings table (`ap_automove`, `ap_automove_group`, `ap_clearqueue`). An update of b1gMail creates them by itself. If you only replaced the files without running the update: back up the database, open _Tools_ > _Optimize_, choose _Check structure_ and click _Execute_, then _Repair structure_. Save the new settings only afterwards.
