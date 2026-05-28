<?php
/*
 * b1gMail – iCalendar (VEVENT) builder for invitations and replies
 */

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

class ICalBuilder
{
    /**
     * @param array $event Event data from ICalReader or calendar row
     * @param string $method REQUEST|REPLY|CANCEL
     * @param string $partstat ACCEPTED|DECLINED|TENTATIVE (REPLY only)
     * @param array $organizer ['email' => '', 'cn' => '']
     * @param array $attendee ['email' => '', 'cn' => '']
     *
     * @return string
     */
    public static function Build($event, $method, $partstat, $organizer, $attendee)
    {
        $lines = [];
        $lines[] = 'BEGIN:VCALENDAR';
        $lines[] = 'VERSION:2.0';
        $lines[] = 'PRODID:-//b1gMail//Calendar//DE';
        $lines[] = 'CALSCALE:GREGORIAN';
        $lines[] = 'METHOD:' . strtoupper($method);
        $lines[] = 'BEGIN:VEVENT';
        $lines[] = 'UID:' . self::_uid($event);
        $lines[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
        $lines[] = 'SEQUENCE:' . (isset($event['sequence']) ? (int) $event['sequence'] : 0);

        if (!empty($organizer['email'])) {
            $lines[] = self::_prop('ORGANIZER', 'mailto:' . $organizer['email'], [
                'CN' => $organizer['cn'] ?? $organizer['email'],
            ]);
        }

        if (!empty($attendee['email'])) {
            $attParams = ['CN' => $attendee['cn'] ?? $attendee['email'], 'RSVP' => 'TRUE'];
            if (strtoupper($method) === 'REPLY' && $partstat !== '') {
                $attParams['PARTSTAT'] = strtoupper($partstat);
            }
            $lines[] = self::_prop('ATTENDEE', 'mailto:' . $attendee['email'], $attParams);
        }

        if (!empty($event['title'])) {
            $lines[] = 'SUMMARY:' . self::_escape($event['title']);
        }
        if (!empty($event['location'])) {
            $lines[] = 'LOCATION:' . self::_escape($event['location']);
        }
        if (!empty($event['text'])) {
            $lines[] = 'DESCRIPTION:' . self::_escape($event['text']);
        }

        if (!empty($event['dtstart_raw'])) {
            $lines[] = self::_rawLine('DTSTART', $event['dtstart_raw']);
        } else {
            $lines[] = self::_dateTimeProp('DTSTART', $event['startdate'], !empty($event['wholeDay']));
        }

        if (!empty($event['dtend_raw'])) {
            $lines[] = self::_rawLine('DTEND', $event['dtend_raw']);
        } else {
            $lines[] = self::_dateTimeProp('DTEND', $event['enddate'], !empty($event['wholeDay']));
        }

        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Build event array from calendar DB row for REQUEST.
     *
     * @param array $row
     *
     * @return array
     */
    public static function EventFromCalendarRow($row)
    {
        return [
            'uid' => !empty($row['dav_uid']) ? $row['dav_uid'] : ('b1gmail-' . $row['id'] . '@' . ($_SERVER['HTTP_HOST'] ?? 'local')),
            'title' => $row['title'] ?? '',
            'location' => $row['location'] ?? '',
            'text' => $row['text'] ?? '',
            'startdate' => (int) $row['startdate'],
            'enddate' => (int) $row['enddate'],
            'wholeDay' => !empty($row['flags']) && ($row['flags'] & CLNDR_WHOLE_DAY),
            'sequence' => 0,
        ];
    }

    /**
     * @param array $event
     * @param string $uid
     */
    private static function _uid($event)
    {
        if (!empty($event['uid'])) {
            return preg_replace('/[^a-zA-Z0-9\-@._]/', '', $event['uid']);
        }

        return 'b1gmail-' . uniqid('', true) . '@' . preg_replace('/[^a-zA-Z0-9.\-]/', '', $_SERVER['HTTP_HOST'] ?? 'local');
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $params
     */
    private static function _prop($name, $value, $params = [])
    {
        $key = $name;
        foreach ($params as $p => $v) {
            if ($v === '' || $v === null) {
                continue;
            }
            $key .= ';' . $p . '=' . self::_escapeParam((string) $v);
        }

        return $key . ':' . $value;
    }

    /**
     * @param string $name
     * @param array  $raw
     */
    private static function _rawLine($name, $raw)
    {
        $key = $name;
        if (!empty($raw['params']) && is_array($raw['params'])) {
            foreach ($raw['params'] as $p => $v) {
                $key .= ';' . $p . '=' . self::_escapeParam((string) $v);
            }
        }

        return $key . ':' . ($raw['value'] ?? '');
    }

    /**
     * @param string $name
     * @param int    $ts
     * @param bool   $wholeDay
     */
    private static function _dateTimeProp($name, $ts, $wholeDay)
    {
        if ($wholeDay) {
            return $name . ';VALUE=DATE:' . gmdate('Ymd', $ts);
        }

        $tzName = date_default_timezone_get();
        if ($tzName && $tzName !== 'UTC') {
            $dt = new DateTime('@' . $ts);
            $dt->setTimezone(new DateTimeZone($tzName));

            return $name . ';TZID=' . self::_escapeParam($tzName) . ':' . $dt->format('Ymd\THis');
        }

        return $name . ':' . gmdate('Ymd\THis\Z', $ts);
    }

    /**
     * @param string $text
     */
    private static function _escape($text)
    {
        $text = str_replace(["\r\n", "\r", "\n"], '\n', $text);

        return str_replace(['\\', ';', ','], ['\\\\', '\;', '\,'], $text);
    }

    /**
     * @param string $text
     */
    private static function _escapeParam($text)
    {
        if (preg_match('/[,;:"]/', $text)) {
            return '"' . str_replace('"', '\\"', $text) . '"';
        }

        return $text;
    }
}
