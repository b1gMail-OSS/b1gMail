# Migration from the commercial to the GPL version

Read here: https://www.b1gmail.eu/forum/thread/9-b1gmail-7-4-1-beta-1-ver%C3%B6ffentlicht/

One important thing to note is that the B1GMAIL_SIGNKEY from the commercial version's `init.inc.php` has to be moved to `config.inc.php` in order to prevent data loss, especially w.r.t. to encrypted resources.