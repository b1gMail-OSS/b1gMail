<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

/**
 * db controller class.
 */
class DB
{
    public $_handle;		// mysql handle
    private $_qcount;		// query count
    private $_last_handle;	// last mysql handle
    private $_current_charset;	// current charset

    /**
     * constructor.
     *
     * @param resource $handle MySQL connection
     */
    public function __construct($handle)
    {
        $this->_handle = $handle;
        $this->_qcount = 0;
        $this->_current_charset = false;
    }

    /**
     * set connection charset.
     *
     * @param string $charset
     */
    public function SetCharset($charset)
    {
        $this->_current_charset = $charset;
        mysqli_set_charset($this->_handle, $charset);
    }

    /**
     * get server version.
     *
     * @return string
     */
    public function GetServerVersion()
    {
        return mysqli_get_server_info($this->_handle);
    }

    /**
     * escape a string for use in SQL query.
     *
     * @param string $str String
     *
     * @return string
     */
    public function Escape($str)
    {
        return mysqli_real_escape_string($this->_handle, $str);
    }

    /**
     * execute safe query.
     *
     * @param string $query
     *
     * @return DB_Result
     */
    public function Query($query)
    {
        global $bm_modules, $mysql;

        // replace {pre} with prefix
        $query = str_replace('{pre}', $mysql['prefix'], $query);

        // insert escaped values, if any
        if (func_num_args() > 1) {
            $args = func_get_args();
            $pos = 0;
            for ($i = 1; $i < func_num_args(); ++$i) {
                $pos = strpos($query, '?', $pos);
                if ($pos === false) {
                    $szUsername = $args[$i];
                    break;
                } else {
                    if (is_string($args[$i]) && (strcmp($args[$i], '#NULL#') == 0)) {
                        $intxt = 'NULL';
                    } elseif (is_array($args[$i])) {
                        $intxt = '';
                        foreach ($args[$i] as $val) {
                            $intxt .= ',\''.$this->Escape($val).'\'';
                        }
                        $intxt = '('.substr($intxt, 1).')';
                        if ($intxt == '()') {
                            $intxt = '(0)';
                        }
                    } else {
                        $intxt = '\''.$this->Escape($args[$i]).'\'';
                    }

                    $query = substr_replace($query, $intxt, $pos, 1);
                    $pos += strlen($intxt);
                }
            }
        }

        // has a module a better handle?
        $handle = $this->_handle;
        if (is_array($bm_modules)) {
            foreach ($bm_modules as $mKey => $module) {
                if ($bm_modules[$mKey]->Features('MySQLHandle')) {
                    $handle = $bm_modules[$mKey]->MySQLHandle($query, $handle, $szUsername);
                }
            }
        }

        $ok = ($result = mysqli_query($handle, $query));

        // try one re-connect on timeout (might happen in pipe keep-alive mode)
        if (!$ok && mysqli_errno($handle) == 2006) {
            $handle = @mysqli_connect($mysql['host'], $mysql['user'], $mysql['pass']);
            if ($handle) {
                if (@mysqli_select_db($handle, $mysql['db'])) {
                    @mysqli_query($handle, 'SET SESSION sql_mode=\'\'');
                    $this->_handle = $handle;
                    if ($this->_current_charset) {
                        $this->SetCharset($this->_current_charset);
                    }
                    $ok = ($result = mysqli_query($handle, $query));
                }
            }
        }

        // increment query count
        ++$this->_qcount;

        // set last handle
        $this->_last_handle = $handle;

        // return new MySQL_Result object if query was successful
        if ($ok) {
            return isset($result) ? new DB_Result($handle, $result, $query) : false;
        } else {
            if (strpos(strtolower($query), 'insert into '.strtolower($mysql['prefix']).'logs') !== false) {
                // log table corrupt -> error page! (would end up in endless loop otherwise)
                DisplayError(0x05, 'Log table error', 'Failed to write log entry to '.$mysql['prefix'].'logs-table. Please check and repair the table.',
                    sprintf("Process:\n%s\n\nError number:\n%d\n\nError description:\n%s",
                        'Query',
                        mysqli_errno($handle),
                        mysqli_error($handle)),
                    __FILE__,
                    __LINE__);
                die();
            } elseif (DEBUG) {
                // debug mode -> error page!
                DisplayError(0x09, 'MySQL error', 'Failed to execute MySQL query.',
                    sprintf("Process:\n%s\n\nQuery:\n%s\n\nError number:\n%d\n\nError description:\n%s",
                        'Query',
                        $query,
                        mysqli_errno($handle),
                        mysqli_error($handle)),
                    __FILE__,
                    __LINE__);
            }
            PutLog("MySQL-Error at '".$_SERVER['SCRIPT_NAME']."': '".mysqli_error($handle)."', tried to execute '".$query."'", PRIO_ERROR, __FILE__, __LINE__);
            if (DEBUG) {
                die();
            }

            return false;
        }
    }

    /**
     * get insert id.
     *
     * @return int
     */
    public function InsertId()
    {
        return mysqli_insert_id($this->_last_handle);
    }

    /**
     * get number of affected rows.
     *
     * @return int
     */
    public function AffectedRows()
    {
        return mysqli_affected_rows($this->_last_handle);
    }
}

/**
 * db result class.
 */
class DB_Result
{
    public $_handle;		// mysql handle
    private $_result;		// mysql result
    private $_query;

    /**
     * constructor.
     *
     * @param resource $handle
     * @param resource $result
     *
     * @return DB_Result
     */
    public function __construct($handle, $result, $query = '')
    {
        $this->_handle = $handle;
        $this->_result = $result;
        $this->_query = $query;
    }

    /**
     * fetch a row as array.
     *
     * @return array
     */
    public function FetchArray($resultType = MYSQLI_BOTH)
    {
        return mysqli_fetch_array($this->_result, $resultType);
    }

    /**
     * fetch a row as object.
     *
     * @return object
     */
    public function FetchObject()
    {
        return mysqli_fetch_object($this->_result);
    }

    /**
     * get count of rows in result set.
     *
     * @return int
     */
    public function RowCount()
    {
        return mysqli_num_rows($this->_result);
    }

    /**
     * get field count.
     *
     * @return int
     */
    public function FieldCount()
    {
        return mysqli_num_fields($this->_result);
    }

    /**
     * get field name.
     *
     * @param int $index Index
     *
     * @return string
     */
    public function FieldName($index)
    {
        $field = mysqli_fetch_field_direct($this->_result, $index);

        return $field->name;
    }

    /**
     * free result.
     */
    public function Free()
    {
        @mysqli_free_result($this->_result);
    }

    /**
     * export result set as CSV.
     *
     * @param string $lineBreakChar Line break character
     * @param string $quoteChar     Quoting character
     * @param string $sepChar       Seperator character
     */
    public function ExportCSV($lineBreakChar = "\n", $quoteChar = '"', $sepChar = ';')
    {
        // get fields
        $fields = [];
        for ($i = 0; $i < $this->FieldCount(); ++$i) {
            $fields[] = $this->FieldName($i);
        }

        // print field list
        $fieldList = '';
        foreach ($fields as $field) {
            $fieldList .= $sepChar.$quoteChar.addslashes($field).$quoteChar;
        }
        $fieldList = substr($fieldList, 1).$lineBreakChar;
        echo $fieldList;

        // print data
        while ($row = $this->FetchArray(MYSQLI_ASSOC)) {
            $columnList = '';
            foreach ($fields as $field) {
                $columnList .= $sepChar.$quoteChar.addslashes($row[$field]).$quoteChar;
            }
            $columnList = substr($columnList, 1).$lineBreakChar;
            echo $columnList;
        }
    }
}
