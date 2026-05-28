<?php
/*
 * b1gMail – iCalendar (VEVENT) reader for mail attachment preview/import
 */

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

/**
 * iCalendar reader (VEVENT components).
 */
class ICalReader
{
    private $_fp;

    /**
     * @param resource $fp ICS file handle
     */
    public function __construct($fp)
    {
        $this->_fp = $fp;
        fseek($this->_fp, 0, SEEK_SET);
    }

    /**
     * @return array[] List of event arrays
     */
    public function Parse()
    {
        $lines = $this->_readLines();
        $method = '';
        $events = [];
        $inEvent = false;
        $current = [];

        foreach ($lines as $line) {
            if ($line === 'BEGIN:VEVENT') {
                $inEvent = true;
                $current = [];
                continue;
            }

            if ($line === 'END:VEVENT') {
                if ($inEvent) {
                    $event = $this->_buildEvent($current, $method);
                    if ($event !== false) {
                        $events[] = $event;
                    }
                }
                $inEvent = false;
                $current = [];
                continue;
            }

            if ($inEvent) {
                $this->_parseLineInto($line, $current);
                continue;
            }

            if (stripos($line, 'METHOD:') === 0) {
                $method = strtoupper(trim(substr($line, 7)));
            }
        }

        return $events;
    }

    /**
     * @param string $line
     * @param array  $target
     */
    private function _parseLineInto($line, &$target)
    {
        $colon = strpos($line, ':');
        if ($colon === false) {
            return;
        }

        $keyPart = substr($line, 0, $colon);
        $value = trim(substr($line, $colon + 1));
        $parsedKey = $this->_parseKey($keyPart);
        $name = $parsedKey['name'];

        if (!isset($target[$name])) {
            $target[$name] = ['value' => $value, 'params' => $parsedKey['params']];
        }
    }

    /**
     * @return string[]
     */
    private function _readLines()
    {
        $lines = [];
        $buffer = '';

        while (is_resource($this->_fp) && !feof($this->_fp)) {
            $raw = rtrim(fgets($this->_fp, 8192), "\r\n");
            if ($raw === false) {
                break;
            }

            if ($raw !== '' && ($raw[0] === ' ' || $raw[0] === "\t")) {
                $buffer .= substr($raw, 1);
                continue;
            }

            if ($buffer !== '') {
                $lines[] = $buffer;
            }
            $buffer = $raw;
        }

        if ($buffer !== '') {
            $lines[] = $buffer;
        }

        return $lines;
    }

    /**
     * @param string $keyPart
     *
     * @return array
     */
    private function _parseKey($keyPart)
    {
        $parts = explode(';', $keyPart);
        $name = strtoupper(trim($parts[0]));
        $params = [];

        for ($i = 1, $n = count($parts); $i < $n; $i++) {
            $eq = strpos($parts[$i], '=');
            if ($eq !== false) {
                $pName = strtoupper(trim(substr($parts[$i], 0, $eq)));
                $pVal = trim(substr($parts[$i], $eq + 1), '"');
                $params[$pName] = $pVal;
            }
        }

        return ['name' => $name, 'params' => $params];
    }

    /**
     * @param array  $fields
     * @param string $method
     *
     * @return array|false
     */
    private function _buildEvent($fields, $method)
    {
        if (!isset($fields['DTSTART'])) {
            return false;
        }

        $start = $this->_parseDateTime($fields['DTSTART']['value'], $fields['DTSTART']['params']);
        if ($start === false) {
            return false;
        }

        $wholeDay = !empty($start['wholeDay']);
        $startdate = $start['ts'];

        if (isset($fields['DTEND'])) {
            $end = $this->_parseDateTime($fields['DTEND']['value'], $fields['DTEND']['params']);
            $enddate = ($end !== false) ? $end['ts'] : $startdate + ($wholeDay ? TIME_ONE_DAY - 60 : TIME_ONE_HOUR);
        } elseif (isset($fields['DURATION'])) {
            $enddate = $startdate + $this->_parseDuration($fields['DURATION']['value']);
        } else {
            $enddate = $startdate + ($wholeDay ? TIME_ONE_DAY - 60 : TIME_ONE_HOUR);
        }

        if ($enddate <= $startdate) {
            $enddate = $startdate + ($wholeDay ? TIME_ONE_DAY - 60 : TIME_ONE_HOUR);
        }

        $organizer = $this->_parseAddressField($fields['ORGANIZER'] ?? null);
        $attendee = $this->_parseAddressField($fields['ATTENDEE'] ?? null);
        $attendeePartstat = '';

        if (isset($fields['ATTENDEE']['params']['PARTSTAT'])) {
            $attendeePartstat = strtoupper($fields['ATTENDEE']['params']['PARTSTAT']);
        }

        return [
            'title' => isset($fields['SUMMARY']) ? $this->_decodeText($fields['SUMMARY']['value']) : '',
            'location' => isset($fields['LOCATION']) ? $this->_decodeText($fields['LOCATION']['value']) : '',
            'text' => isset($fields['DESCRIPTION']) ? $this->_decodeText($fields['DESCRIPTION']['value']) : '',
            'startdate' => $startdate,
            'enddate' => $enddate,
            'wholeDay' => $wholeDay,
            'uid' => isset($fields['UID']) ? trim($fields['UID']['value']) : '',
            'sequence' => isset($fields['SEQUENCE']) ? (int) $fields['SEQUENCE']['value'] : 0,
            'method' => $method,
            'organizer_email' => $organizer['email'],
            'organizer_cn' => $organizer['cn'],
            'attendee_email' => $attendee['email'],
            'attendee_cn' => $attendee['cn'],
            'attendee_partstat' => $attendeePartstat,
            'dtstart_raw' => $fields['DTSTART'],
            'dtend_raw' => $fields['DTEND'] ?? null,
        ];
    }

    /**
     * @param array|null $field
     *
     * @return array{email: string, cn: string}
     */
    private function _parseAddressField($field)
    {
        $result = ['email' => '', 'cn' => ''];

        if ($field === null) {
            return $result;
        }

        $value = $field['value'];
        if (preg_match('/mailto:([^;>\s]+)/i', $value, $m)) {
            $result['email'] = strtolower($m[1]);
        }

        if (!empty($field['params']['CN'])) {
            $result['cn'] = $this->_decodeText($field['params']['CN']);
        } elseif ($result['email'] !== '') {
            $result['cn'] = $result['email'];
        }

        return $result;
    }

    /**
     * @param string $value
     * @param array  $params
     *
     * @return array|false
     */
    private function _parseDateTime($value, $params)
    {
        $value = trim($value);
        $wholeDay = isset($params['VALUE']) && strtoupper($params['VALUE']) === 'DATE';

        if ($wholeDay || preg_match('/^\d{8}$/', preg_replace('/[^0-9]/', '', $value))) {
            $digits = preg_replace('/[^0-9]/', '', $value);
            if (strlen($digits) < 8) {
                return false;
            }
            $tz = $this->_resolveTimezone($params['TZID'] ?? null);
            $dt = DateTime::createFromFormat('Ymd', substr($digits, 0, 8), $tz);
            if (!$dt) {
                return false;
            }

            return ['ts' => $dt->getTimestamp(), 'wholeDay' => true];
        }

        $digits = preg_replace('/[^0-9TZ]/', '', strtoupper($value));
        $isUtc = (substr($digits, -1) === 'Z');
        if ($isUtc) {
            $digits = substr($digits, 0, -1);
        }

        $format = 'Ymd\THis';
        if (strlen($digits) === 12) {
            $format = 'Ymd\THi';
        } elseif (strlen($digits) < 15) {
            return false;
        }

        if ($isUtc) {
            $tz = new DateTimeZone('UTC');
        } else {
            $tz = $this->_resolveTimezone($params['TZID'] ?? null);
        }

        $dt = DateTime::createFromFormat($format, $digits, $tz);
        if (!$dt) {
            return false;
        }

        return ['ts' => $dt->getTimestamp(), 'wholeDay' => false];
    }

    /**
     * @param string|null $tzid
     *
     * @return DateTimeZone
     */
    private function _resolveTimezone($tzid)
    {
        if ($tzid !== null && $tzid !== '') {
            $tzid = trim($tzid, '"');
            try {
                return new DateTimeZone($tzid);
            } catch (Exception $e) {
                if (stripos($tzid, 'Zurich') !== false || stripos($tzid, 'Berlin') !== false) {
                    try {
                        return new DateTimeZone('Europe/Zurich');
                    } catch (Exception $e2) {
                    }
                }
            }
        }

        return new DateTimeZone(date_default_timezone_get());
    }

    /**
     * @param string $value
     *
     * @return int seconds
     */
    private function _parseDuration($value)
    {
        if (!preg_match('/^P(?:(\d+)D)?(?:T(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?)?$/i', trim($value), $m)) {
            return TIME_ONE_HOUR;
        }

        $days = isset($m[1]) ? (int) $m[1] : 0;
        $hours = isset($m[2]) ? (int) $m[2] : 0;
        $minutes = isset($m[3]) ? (int) $m[3] : 0;
        $seconds = isset($m[4]) ? (int) $m[4] : 0;

        return $days * TIME_ONE_DAY + $hours * TIME_ONE_HOUR + $minutes * TIME_ONE_MINUTE + $seconds;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    private function _decodeText($text)
    {
        $text = str_replace(['\\n', '\\N', '\\,', '\\;'], ["\n", "\n", ',', ';'], $text);

        return $text;
    }
}
