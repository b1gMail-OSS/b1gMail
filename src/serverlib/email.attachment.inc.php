<?php
/*
 * b1gMail – E-Mail-Anhang: Typ-Erkennung und Vorschau-Hilfen
 */

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

if (!defined('BM_MAIL_ICS_PARSE_MAX_BYTES')) {
    define('BM_MAIL_ICS_PARSE_MAX_BYTES', 1024 * 1024);
}

/**
 * Load BMMailBuilder (not included on all organizer pages).
 */
function bmMailEnsureMailBuilder()
{
    if (!class_exists('BMMailBuilder')) {
        include B1GMAIL_DIR.'serverlib/mailbuilder.class.php';
    }
}

/**
 * @param string $mimetype Full MIME type (may include parameters)
 *
 * @return string Base type, lowercased (e.g. text/calendar)
 */
function bmMailNormalizeMimeType($mimetype)
{
    $mimetype = strtolower(trim($mimetype));
    $semi = strpos($mimetype, ';');

    if ($semi !== false) {
        $mimetype = trim(substr($mimetype, 0, $semi));
    }

    return $mimetype;
}

/**
 * @param string $mimetype
 *
 * @return string METHOD value from parameters or empty
 */
function bmMailCalendarMethodFromMime($mimetype)
{
    if (preg_match('/\bmethod\s*=\s*([a-z]+)/i', $mimetype, $m)) {
        return strtoupper($m[1]);
    }

    return '';
}

/**
 * @param array $info Attachment info from BMMail::GetAttachments()
 *
 * @return string vcf|ics|viewable|default
 */
function bmMailAttachmentOpenKind($info)
{
    global $VIEWABLE_TYPES;

    $mimetype = bmMailNormalizeMimeType($info['mimetype'] ?? '');
    $filename = strtolower($info['filename'] ?? '');
    $filetype = strtolower($info['filetype'] ?? '');

    if (bmMailAttachmentIsVcf($mimetype, $filename)) {
        return 'vcf';
    }

    if (bmMailAttachmentIsIcs($mimetype, $filename, $filetype)) {
        return 'ics';
    }

    if (in_array($mimetype, $VIEWABLE_TYPES, true)) {
        return 'viewable';
    }

    return 'default';
}

/**
 * @param string $mimetype
 * @param string $filename
 */
function bmMailAttachmentIsVcf($mimetype, $filename)
{
    $mimetype = bmMailNormalizeMimeType($mimetype);

    return in_array($mimetype, [
        'text/directory',
        'text/x-vcard',
        'application/vcard',
        'text/anytext',
        'application/x-versit',
        'text/x-versit',
    ], true)
        || (in_array($mimetype, ['text/plain', 'application/octet-stream'], true)
            && stripos($filename, '.vcf') !== false);
}

/**
 * @param string $mimetype
 * @param string $filename
 * @param string $filetype
 */
function bmMailAttachmentIsIcs($mimetype, $filename, $filetype = '')
{
    $mimetype = bmMailNormalizeMimeType($mimetype);

    if (in_array($mimetype, [
        'text/calendar',
        'application/ics',
        'application/octet-stream',
        'text/x-vcalendar',
    ], true)) {
        return stripos($filename, '.ics') !== false
            || $filetype === '.ics'
            || $mimetype === 'text/calendar'
            || $mimetype === 'application/ics'
            || $mimetype === 'text/x-vcalendar';
    }

    return stripos($filename, '.ics') !== false
        || $filetype === '.ics';
}

/**
 * @param BMMail $mail
 * @param string $attachmentKey
 *
 * @return array|false
 */
function bmMailParseVcfAttachment($mail, $attachmentKey)
{
    if (!class_exists('VCardReader')) {
        include B1GMAIL_DIR.'serverlib/vcard.class.php';
    }

    $tempID = RequestTempFile($mail->_userID, time() + TIME_ONE_MINUTE);
    $cardFP = fopen(TempFileName($tempID), 'w+');

    if (!$mail->AttachmentToFP($attachmentKey, $cardFP)) {
        fclose($cardFP);
        ReleaseTempFile($mail->_userID, $tempID);

        return false;
    }

    fseek($cardFP, 0, SEEK_SET);
    $reader = _new('VCardReader', [$cardFP]);
    $cardData = $reader->Parse();
    fclose($cardFP);
    ReleaseTempFile($mail->_userID, $tempID);

    return $cardData;
}

/**
 * @param BMMail $mail
 * @param string $attachmentKey
 *
 * @return array|false First event preview or false
 */
/**
 * @param BMMail $mail
 * @param string $attachmentKey
 *
 * @return int
 */
function bmMailGetAttachmentSize($mail, $attachmentKey)
{
    $parts = $mail->GetPartList();

    if (!isset($parts[$attachmentKey])) {
        return 0;
    }

    return (int) $mail->EstimatePartSize($parts[$attachmentKey]);
}

/**
 * sprintf() safe for user-supplied strings that may contain % characters.
 *
 * @param string $format
 * @param mixed  ...$args
 *
 * @return string
 */
function bmMailSafeSprintf($format, ...$args)
{
    $n = 1;
    $normalized = $format;

    while (($pos = strpos($normalized, '%s')) !== false) {
        $normalized = substr_replace($normalized, '%'.$n.'$s', $pos, 2);
        $n++;
    }

    return vsprintf($normalized, $args);
}

function bmMailParseIcsAttachment($mail, $attachmentKey)
{
    if (bmMailGetAttachmentSize($mail, $attachmentKey) > BM_MAIL_ICS_PARSE_MAX_BYTES) {
        return false;
    }

    if (!class_exists('ICalReader')) {
        include B1GMAIL_DIR.'serverlib/ical.class.php';
    }

    $tempID = RequestTempFile($mail->_userID, time() + TIME_ONE_MINUTE);
    $icsFP = fopen(TempFileName($tempID), 'w+');

    if (!$mail->AttachmentToFP($attachmentKey, $icsFP)) {
        fclose($icsFP);
        ReleaseTempFile($mail->_userID, $tempID);

        return false;
    }

    fseek($icsFP, 0, SEEK_SET);
    $reader = _new('ICalReader', [$icsFP]);
    $events = $reader->Parse();
    fclose($icsFP);
    ReleaseTempFile($mail->_userID, $tempID);

    if (!is_array($events) || count($events) === 0) {
        return false;
    }

    return $events[0];
}

/**
 * @param array $attachments
 */
function bmMailEnrichAttachmentOpenKinds(&$attachments)
{
    foreach ($attachments as $key => &$info) {
        $info['openKind'] = bmMailAttachmentOpenKind($info);
    }
    unset($info);
}

/**
 * Redirect the parent window (overlay host) or the current window.
 *
 * @param string $url Target URL
 */
/**
 * @param array   $event Parsed ICS event
 * @param BMMail  $mail  Source mail (optional, for organizer fallback)
 *
 * @return bool
 */
function bmMailCanSendCalendarReply($event, $mail = null)
{
    if (empty($event['organizer_email']) && $mail !== null) {
        $from = ParseMailList($mail->GetHeaderValue('from'));
        if (count($from) > 0 && !empty($from[0]['mail'])) {
            return true;
        }

        return false;
    }

    if (empty($event['organizer_email'])) {
        return false;
    }

    $method = strtoupper($event['method'] ?? '');

    return ($method === '' || $method === 'REQUEST' || $method === 'PUBLISH');
}

/**
 * @param array  $event
 * @param BMMail $mail
 */
function bmMailFillOrganizerFromMail($event, $mail)
{
    if (!empty($event['organizer_email'])) {
        return $event;
    }

    $from = ParseMailList($mail->GetHeaderValue('from'));
    if (count($from) > 0 && !empty($from[0]['mail'])) {
        $event['organizer_email'] = strtolower($from[0]['mail']);
        $event['organizer_cn'] = trim($from[0]['name'] ?? '');
        if ($event['organizer_cn'] === '') {
            $event['organizer_cn'] = $event['organizer_email'];
        }
    }

    return $event;
}

/**
 * Map RSVP action / ICS PARTSTAT to stored and iCal values.
 *
 * @param string $partstat accepted|declined|tentative|ACCEPTED|...
 *
 * @return array{store: string, ical: string}
 */
function bmMailCalendarPartstatMap($partstat)
{
    $key = strtolower(str_replace('_', '-', trim($partstat)));

    $map = [
        'accepted' => ['store' => 'accepted', 'ical' => 'ACCEPTED'],
        'declined' => ['store' => 'declined', 'ical' => 'DECLINED'],
        'tentative' => ['store' => 'tentative', 'ical' => 'TENTATIVE'],
        'needs-action' => ['store' => 'needs-action', 'ical' => 'NEEDS-ACTION'],
    ];

    if (isset($map[$key])) {
        return $map[$key];
    }

    $upper = strtoupper(str_replace('-', '_', $key));
    if ($upper === 'ACCEPTED') {
        return $map['accepted'];
    }
    if ($upper === 'DECLINED') {
        return $map['declined'];
    }
    if ($upper === 'TENTATIVE') {
        return $map['tentative'];
    }

    return $map['accepted'];
}

/**
 * @param BMMail $mail
 *
 * @return array|false First calendar event from any ICS attachment
 */
function bmMailFindCalendarEventInMail($mail)
{
    $attachments = $mail->GetAttachments();

    foreach ($attachments as $key => $info) {
        if (!bmMailAttachmentIsIcs(
            $info['mimetype'] ?? '',
            $info['filename'] ?? '',
            $info['filetype'] ?? ''
        )) {
            continue;
        }

        $event = bmMailParseIcsAttachment($mail, $key);
        if ($event !== false) {
            $event['_attachmentKey'] = $key;

            if (($event['method'] ?? '') === '') {
                $mimeMethod = bmMailCalendarMethodFromMime($info['mimetype'] ?? '');
                if ($mimeMethod !== '') {
                    $event['method'] = $mimeMethod;
                }
            }

            return $event;
        }
    }

    return false;
}

/**
 * Minimal event data when ICS parsing fails but a calendar attachment exists.
 *
 * @param BMMail $mail
 *
 * @return array|false
 */
function bmMailBuildCalendarEventFallback($mail)
{
    $attachments = $mail->GetAttachments();
    $attachmentKey = null;
    $mimeMethod = '';

    foreach ($attachments as $key => $info) {
        if (!bmMailAttachmentIsIcs(
            $info['mimetype'] ?? '',
            $info['filename'] ?? '',
            $info['filetype'] ?? ''
        )) {
            continue;
        }

        $attachmentKey = $key;
        $mimeMethod = bmMailCalendarMethodFromMime($info['mimetype'] ?? '');
        break;
    }

    if ($attachmentKey === null) {
        return false;
    }

    $subject = $mail->GetHeaderValue('subject');
    $title = $subject;
    if (preg_match('/^(?:Termineinladung|Kalendereinladung|Einladung|Invitation)\s*:\s*["\']?(.+?)["\']?\s*$/iu', $subject, $m)) {
        $title = trim($m[1]);
    }

    $from = ParseMailList($mail->GetHeaderValue('from'));

    return [
        'title' => $title,
        'location' => '',
        'text' => '',
        'startdate' => (int) $mail->date,
        'enddate' => (int) $mail->date + (defined('TIME_ONE_HOUR') ? TIME_ONE_HOUR : 3600),
        'wholeDay' => false,
        'uid' => '',
        'method' => $mimeMethod !== '' ? $mimeMethod : 'REQUEST',
        'organizer_email' => count($from) > 0 ? strtolower($from[0]['mail'] ?? '') : '',
        'organizer_cn' => count($from) > 0 ? trim($from[0]['name'] ?? '') : '',
        '_attachmentKey' => $attachmentKey,
    ];
}

/**
 * @param array $event
 * @param array $userRow
 *
 * @return bool True if the logged-in user is the organizer (no RSVP to self)
 */
function bmMailIsOwnCalendarInvite($event, $userRow)
{
    $organizer = strtolower(trim($event['organizer_email'] ?? ''));
    $userMail = strtolower(trim($userRow['email'] ?? ''));

    return $organizer !== '' && $userMail !== '' && $organizer === $userMail;
}

/**
 * @param BMMail  $mail
 * @param array   $userRow
 *
 * @return array|false Invite card data for template
 */
function bmMailGetCalendarInviteCard($mail, $userRow)
{
    $event = bmMailFindCalendarEventInMail($mail);

    if ($event === false) {
        $event = bmMailBuildCalendarEventFallback($mail);
    }

    if ($event === false) {
        return false;
    }

    $event = bmMailFillOrganizerFromMail($event, $mail);
    $method = strtoupper($event['method'] ?? '');

    if ($method === 'REPLY') {
        return bmMailGetCalendarReplyNoticeCard($mail, $userRow, $event);
    }

    if ($method !== '' && $method !== 'REQUEST' && $method !== 'PUBLISH') {
        return false;
    }

    $canReply = bmMailCanSendCalendarReply($event, $mail)
        && !bmMailIsOwnCalendarInvite($event, $userRow);

    return [
        'type' => 'invite',
        'event' => $event,
        'attachment' => (string) ($event['_attachmentKey'] ?? ''),
        'canReply' => $canReply,
    ];
}

/**
 * @param BMMail $mail
 * @param array  $userRow
 * @param array  $event
 *
 * @return array|false
 */
function bmMailGetCalendarReplyNoticeCard($mail, $userRow, $event)
{
    global $lang_user;

    if (!class_exists('BMCalendar')) {
        include B1GMAIL_DIR.'serverlib/calendar.class.php';
    }

    $from = ParseMailList($mail->GetHeaderValue('from'));
    $attendeeEmail = !empty($event['attendee_email'])
        ? strtolower($event['attendee_email'])
        : (count($from) > 0 ? strtolower($from[0]['mail'] ?? '') : '');
    $attendeeName = !empty($event['attendee_cn'])
        ? $event['attendee_cn']
        : (count($from) > 0 ? trim($from[0]['name'] ?? '') : $attendeeEmail);

    if ($attendeeName === '') {
        $attendeeName = $attendeeEmail;
    }

    $partstat = bmMailCalendarPartstatMap($event['attendee_partstat'] ?? 'accepted');
    $processed = bmMailProcessCalendarReplyForUser($userRow, $event, $attendeeEmail, $partstat['store']);

    $dateID = 0;
    $dateTitle = $event['title'];
    $calendar = _new('BMCalendar', [$userRow['id']]);
    $dateRow = !empty($event['uid'])
        ? $calendar->FindDateByDavUid($event['uid'])
        : $calendar->FindDateByTitle($event['title'] ?? '');
    if ($dateRow !== false) {
        $dateID = (int) $dateRow['id'];
        if ($dateTitle === '') {
            $dateTitle = $dateRow['title'];
        }
    }

    $bodyKey = 'mail_att_reply_notice_'.$partstat['store'];
    if (!isset($lang_user[$bodyKey])) {
        $bodyKey = 'mail_att_reply_notice_accepted';
    }

    return [
        'type' => 'reply',
        'event' => $event,
        'attendeeName' => $attendeeName,
        'partstat' => $partstat['store'],
        'processed' => $processed,
        'dateID' => $dateID,
        'dateTitle' => $dateTitle,
        'message' => bmMailSafeSprintf($lang_user[$bodyKey], $attendeeName, $dateTitle),
    ];
}

/**
 * Apply incoming iCalendar REPLY to organizer calendar.
 *
 * @return bool
 */
function bmMailProcessCalendarReplyForUser($userRow, $event, $attendeeEmail, $partstat)
{
    if (!class_exists('BMCalendar')) {
        include B1GMAIL_DIR.'serverlib/calendar.class.php';
    }

    if ($attendeeEmail === '') {
        return false;
    }

    $calendar = _new('BMCalendar', [(int) $userRow['id']]);
    $uid = trim($event['uid'] ?? '');
    $dateRow = ($uid !== '')
        ? $calendar->FindDateByDavUid($uid)
        : $calendar->FindDateByTitle($event['title'] ?? '');
    if ($dateRow === false) {
        return false;
    }

    return $calendar->SetAttendeePartstatByEmail((int) $dateRow['id'], $attendeeEmail, $partstat);
}

/**
 * Process calendar reply mail on receive/read (organizer).
 *
 * @param BMMail $mail
 */
function bmMailProcessCalendarReplyMail($userRow, $mail)
{
    if (!is_object($mail) || !method_exists($mail, 'GetAttachments')) {
        return;
    }

    if (!method_exists($mail, 'Parse')) {
        return;
    }

    $event = bmMailFindCalendarEventInMail($mail);
    if ($event === false) {
        $event = bmMailInferCalendarReplyFromHeaders($mail);
        if ($event === false) {
            return;
        }
    }

    $method = strtoupper($event['method'] ?? '');
    if ($method !== 'REPLY' && $method !== '') {
        return;
    }

    $from = ParseMailList($mail->GetHeaderValue('from'));
    $attendeeEmail = !empty($event['attendee_email'])
        ? strtolower($event['attendee_email'])
        : (count($from) > 0 ? strtolower($from[0]['mail'] ?? '') : '');

    $partstat = bmMailCalendarPartstatMap($event['attendee_partstat'] ?? 'accepted');

    bmMailProcessCalendarReplyForUser($userRow, $event, $attendeeEmail, $partstat['store']);
}

/**
 * Fallback when ICS is missing but subject indicates RSVP (e.g. Outlook-style).
 *
 * @param BMMail $mail
 *
 * @return array|false
 */
function bmMailInferCalendarReplyFromHeaders($mail)
{
    $subject = $mail->GetHeaderValue('subject');
    $partstat = 'accepted';
    $title = '';

    if (preg_match('/^(?:Einladung zugestimmt|Angenommen|Accepted)\s*:\s*["\']?(.+?)["\']?\s*$/iu', $subject, $m)) {
        $title = trim($m[1]);
    } elseif (preg_match('/^(?:Einladung abgelehnt|Abgelehnt|Declined)\s*:\s*["\']?(.+?)["\']?\s*$/iu', $subject, $m)) {
        $partstat = 'declined';
        $title = trim($m[1]);
    } elseif (preg_match('/^(?:Vielleicht|Zugesagt mit Vorbehalt|Tentative)\s*:\s*["\']?(.+?)["\']?\s*$/iu', $subject, $m)) {
        $partstat = 'tentative';
        $title = trim($m[1]);
    } else {
        return false;
    }

    return [
        'title' => $title,
        'uid' => '',
        'method' => 'REPLY',
        'attendee_partstat' => $partstat,
        'attendee_email' => '',
    ];
}

/**
 * Send iTIP REPLY to the event organizer.
 *
 * @param string $partstat accepted|declined|tentative
 * @param string $comment  Optional comment for organizer
 *
 * @return bool
 */
function bmMailSendCalendarReply($event, $userRow, $thisUser, $partstat = 'accepted', $comment = '')
{
    global $lang_user, $bm_prefs;

    if (empty($event['organizer_email'])) {
        return false;
    }

    if (!class_exists('ICalBuilder')) {
        include B1GMAIL_DIR.'serverlib/ical.builder.php';
    }

    $mapped = bmMailCalendarPartstatMap($partstat);

    $attendeeCn = trim($userRow['vorname'].' '.$userRow['nachname']);
    if ($attendeeCn === '') {
        $attendeeCn = $userRow['email'];
    }

    $organizer = [
        'email' => $event['organizer_email'],
        'cn' => !empty($event['organizer_cn']) ? $event['organizer_cn'] : $event['organizer_email'],
    ];
    $attendee = [
        'email' => strtolower($userRow['email']),
        'cn' => $attendeeCn,
    ];

    $replyEvent = $event;
    if ($comment !== '') {
        $replyEvent['text'] = trim($comment);
    }

    $icsBody = ICalBuilder::Build($replyEvent, 'REPLY', $mapped['ical'], $organizer, $attendee);

    $senders = $thisUser->GetPossibleSenders();
    $from = count($senders) > 0 ? $senders[0] : sprintf('"%s" <%s>',
        EncodeMailHeaderField($attendeeCn),
        $userRow['email']);

    $subjectKey = 'mail_att_reply_subject_'.$mapped['store'];
    if (!isset($lang_user[$subjectKey])) {
        $subjectKey = 'mail_att_reply_subject';
    }
    $bodyKey = 'mail_att_reply_body_'.$mapped['store'];
    if (!isset($lang_user[$bodyKey])) {
        $bodyKey = 'mail_att_reply_body';
    }

    $eventTitle = !empty($event['title']) ? $event['title'] : $lang_user['calendar'];
    $subject = bmMailSafeSprintf($lang_user[$subjectKey], $eventTitle);
    $body = bmMailSafeSprintf($lang_user[$bodyKey], $attendeeCn, $eventTitle);
    if ($comment !== '') {
        $body .= "\n\n".$comment;
    }

    bmMailEnsureMailBuilder();
    $mail = _new('BMMailBuilder');
    $mail->SetUserID($userRow['id']);
    $mail->AddHeaderField('From', $from);
    $mail->AddHeaderField('To', sprintf('"%s" <%s>',
        EncodeMailHeaderField($organizer['cn']),
        $organizer['email']));
    $mail->AddHeaderField('Subject', $subject);
    $mail->AddText($body, 'plain', 'UTF-8');
    $mail->AddAttachment($icsBody,
        'text/calendar; charset=UTF-8; method=REPLY',
        'invite.ics');

    $outboxFP = $mail->Send();
    $mail->CleanUp();

    return($outboxFP !== false);
}

/**
 * Send calendar REQUEST invitations to address book attendees.
 *
 * @param array $row         Calendar date row
 * @param int[] $attendeeIDs Contact IDs
 *
 * @return int Number of mails sent
 */
function bmCalendarSendInvites($userRow, $thisUser, $row, $attendeeIDs)
{
    global $lang_user;

    if (empty($attendeeIDs)) {
        return 0;
    }

    if (!class_exists('BMAddressbook')) {
        include B1GMAIL_DIR.'serverlib/addressbook.class.php';
    }
    if (!class_exists('ICalBuilder')) {
        include B1GMAIL_DIR.'serverlib/ical.builder.php';
    }

    $addressbook = _new('BMAddressbook', [$userRow['id']]);
    $senders = $thisUser->GetPossibleSenders();
    $organizerCn = trim($userRow['vorname'].' '.$userRow['nachname']);
    if ($organizerCn === '') {
        $organizerCn = $userRow['email'];
    }
    $organizer = ['email' => strtolower($userRow['email']), 'cn' => $organizerCn];
    $event = ICalBuilder::EventFromCalendarRow($row);
    $from = count($senders) > 0 ? $senders[0] : sprintf('"%s" <%s>',
        EncodeMailHeaderField($organizerCn),
        $userRow['email']);
    $sent = 0;

    foreach ($attendeeIDs as $contactID) {
        $contactID = (int) $contactID;
        if ($contactID <= 0) {
            continue;
        }

        $contact = $addressbook->GetContact($contactID);
        if ($contact === false) {
            continue;
        }

        $email = trim($contact['default_address'] == ADDRESS_PRIVATE
            ? $contact['email']
            : $contact['work_email']);
        if ($email === '' || !preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $email)) {
            continue;
        }

        $attendeeCn = trim($contact['vorname'].' '.$contact['nachname']);
        if ($attendeeCn === '') {
            $attendeeCn = $email;
        }

        $icsBody = ICalBuilder::Build($event, 'REQUEST', '', $organizer, [
            'email' => strtolower($email),
            'cn' => $attendeeCn,
        ]);

        bmMailEnsureMailBuilder();
        $mail = _new('BMMailBuilder');
        $mail->SetUserID($userRow['id']);
        $mail->AddHeaderField('From', $from);
        $mail->AddHeaderField('To', sprintf('"%s" <%s>',
            EncodeMailHeaderField($attendeeCn),
            $email));
        $mail->AddHeaderField('Subject', sprintf($lang_user['mail_att_invite_subject'], $row['title']));
        $mail->AddText(sprintf($lang_user['mail_att_invite_body'],
            $organizerCn,
            $row['title']), 'plain', 'UTF-8');
        $mail->AddAttachment($icsBody,
            'text/calendar; charset=UTF-8; method=REQUEST',
            'invite.ics');

        if ($mail->Send() !== false) {
            $sent++;
        }
        $mail->CleanUp();
    }

    return $sent;
}

function bmMailOverlayParentRedirect($url)
{
    global $currentCharset;

    header('Content-Type: text/html; charset=' . $currentCharset);
    echo '<!DOCTYPE html><html><head><meta charset="' . htmlspecialchars($currentCharset, ENT_QUOTES, 'UTF-8') . '"></head><body><script>' . "\n";
    echo 'var u=' . json_encode($url) . ';' . "\n";
    echo 'if(window.parent&&window.parent!==window){' . "\n";
    echo 'if(typeof parent.hideOverlay==="function")parent.hideOverlay();' . "\n";
    echo 'parent.document.location.href=u;' . "\n";
    echo '}else{window.location.href=u;}' . "\n";
    echo '</script></body></html>';
    exit();
}
