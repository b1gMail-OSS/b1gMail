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

if(!defined('B1GMAIL_INIT'))
	die('Directly calling this file is not supported');

define('BMSEARCH_TEXTSIZE_LIMIT',		64*1024);

/**
 * user search index class
 *
 */
class BMSearchIndex
{
	/**
	 * User ID
	 */
	private $userID;

	/**
	 * SQLite db object
	 */
	private $sdb;

	/**
	 * Active transactions counter
	 */
	private $txCounter = 0;

	/**
	 * constructor
	 *
	 * @param int $userID User ID
	 */
	public function __construct($userID)
	{
		$this->userID 	= $userID;

		$dbFileName 	= $this->getDBFileName();
		if(!file_exists($dbFileName))
			@touch($dbFileName);
		@chmod($dbFileName, 0666);
		$this->sdb 		= new SQLite3($dbFileName);

		$this->initDB();
	}

	/**
	 * destructor
	 *
	 */
	public function __destruct()
	{
		$this->sdb->close();
	}

	/**
	 * delete all index entries associated with a certain item
	 *
	 * @param int $itemID
	 */
	public function deleteItem($itemID)
	{
		$stmt = $this->sdb->prepare('DELETE FROM [index] WHERE [itemid]=:itemid');
		$stmt->bindValue(':itemid', $itemID);
		$stmt->execute();

		$stmt = $this->sdb->prepare('DELETE FROM [text] WHERE [itemid]=:itemid');
		$stmt->bindValue(':itemid', $itemID);
		$stmt->execute();
	}

	/**
	 * split a query into tokens
	 *
	 * @param string $query
	 * @return array
	 */
	private function splitQuery($query)
	{
		$words = preg_split('/[^a-z0-9]+/', strtolower($query), -1, PREG_SPLIT_NO_EMPTY);
		return($words);
	}

	/**
	 * search index, return matching items
	 *
	 * @param string $query
	 * @return array
	 */
	public function search($query)
	{
		$result = array();

		$words = $this->splitQuery($query);
		if(count($words) > 0)
		{
			// find words
			$wordList = array();
			foreach($words as $word)
			{
				$escWord = $this->sdb->escapeString($word);

				if(in_array($escWord, $wordList))
					continue;

				$wordList[] = $escWord;
			}
			$wordListStr = '\'' . implode('\',\'', $wordList) . '\'';

			$searchWords = array();
			$res = $this->sdb->query('SELECT [wordid] FROM [word] WHERE [word] IN (' . $wordListStr . ')');
			while($row = $res->fetchArray())
			{
				$searchWords[] = $row['wordid'];
			}
			$res->finalize();

			// find items containing the search words
			if(count($wordList) > 0 && count($wordList) <= count($searchWords))
			{
				$res = $this->sdb->query('SELECT [index].[itemid],COUNT(*) AS [wordCount],[text].[text],SUM([count]) AS [occurenceCount] FROM [index] '
					. 'LEFT JOIN [text] ON [text].[itemid]=[index].[itemid] '
					. 'WHERE [index].[wordid] IN (' . implode(',', $searchWords) . ') '
					. 'GROUP BY [index].[itemid]');
				while($row = $res->fetchArray())
				{
					if($row['wordCount'] < count($wordList))
						continue;

					$resultItem = array(
						'itemID'			=> $row['itemid'],
						'text'				=> $row['text'],
						'occurenceCount'	=> $row['occurenceCount']
					);

					$result[] = $resultItem;
				}
				$res->finalize();
			}
		}

		return($result);
	}

	/**
	 * compute search result score
	 *
	 * @param string $query Search query
	 * @param string $text Text of result
	 * @return float
	 */
	public function computeScore($query, $text)
	{
		$words = array_unique($this->splitQuery($query));
		$textWords = $this->splitQuery($text);
		$result = 0;
		$lastWordPos = 0;

		foreach($words as $word)
		{
			$wordMaxScore = 0;
			$wordPos = 0;
			$i = 0;

			foreach($textWords as $textWord)
			{
				$wordScore = 0;
				similar_text($word, $textWord, $wordScore);
				$wordScore /= 100;

				if($wordScore > $wordMaxScore)
				{
					$wordPos = $i;
					$wordMaxScore = $wordScore;
				}

				if($wordScore == 1)
					break;

				++$i;
			}

			if($lastWordPos > $wordPos)
				$wordMaxScore *= 0.9;

			$result += $wordMaxScore;
			$lastWordPos = $wordPos;
		}

		return($result / count($words));
	}

	/**
	 * create a text excerpt for a match with highlighted matches
	 *
	 * @param string $query Search query
	 * @param string $text Result text
	 * @param int $length Max length of excerpt
	 * @param int $before Number of chars before first matching word
	 * @return string
	 */
	public function createExcerpt($query, $text, $length = 140, $before = 24)
	{
		$result = '';
		$words = array_unique($this->splitQuery($query));

		if(strlen($text) <= $length)
		{
			$result = $text;
		}
		else
		{
			if(count($words))
			{
				$positions = array();
				foreach($words as $word)
				{
					$pos = stripos($text, $word);
					while($pos !== false)
					{
						$positions[] = $pos;
						$pos = stripos($text, $word, $pos + strlen($word));
					}
				}
				sort($positions);

				$startPos = $positions[0];
				$minDiff = strlen($text);

				if(count($positions) > 2)
				{
					for($i=1; $i<count($positions); ++$i)
					{
						if($i == count($positions)-1)
							$posDiff = $positions[$i] - $positions[$i-1];
						else
							$posDiff = $positions[$i+1] - $positions[$i];

						if($posDiff < $minDiff)
						{
							$minDiff = $posDiff;
							$startPos = $positions[$i];
						}
					}
				}

				$startPos = max(0, $startPos-$before);
				if(strlen($text)-$startPos < $length)
					$startPos -= round((strlen($text)-$startPos)/2, 0);

				$result = substr($text, $startPos, $length);

				if($startPos+$length < strlen($text))
					$result = substr($result, 0, strrpos($result, ' ')) . '...';
				if($startPos > 0)
					$result = '...' . $result;
			}
		}

		$result = HTMLFormat($result);

		foreach($words as $word)
		{
			$result = preg_replace('/' . preg_quote($word) . '/i', '<strong>\\0</strong>', $result);
		}

		return($result);
	}

	/**
	 * add text to index
	 *
	 * @param string $text
	 * @param int $itemID
	 */
	public function addTextToIndex($text, $itemID)
	{
		if(strlen($text) >= 3)
		{
			if(strlen($text) > BMSEARCH_TEXTSIZE_LIMIT)
				$text = substr($text, 0, BMSEARCH_TEXTSIZE_LIMIT);
			$lcText = strtolower($text);
			$tokens = $this->splitQuery($text);
			$words = array();

			foreach($tokens as $token)
			{
				if(isset($words[$token]))
				{
					++$words[$token]['count'];
				}
				else
				{
					$words[$token] = array('word' => $token, 'count' => 1);
				}
			}

			if(count($words))
			{
				$this->addToIndex($words, $itemID);

				$stmt = $this->sdb->prepare('REPLACE INTO [text]([itemid],[text]) VALUES(:itemid,:text)');
				$stmt->bindValue(':itemid', $itemID);
				$stmt->bindValue(':text', $text);
				$stmt->execute();
			}
		}
	}

	/**
	 * add words to index
	 *
	 * @param array $words Array of words, each entry has to have a 'word' and a 'count' entry
	 * @param int $itemID
	 */
	public function addToIndex($words, $itemID)
	{
		$wordArray = array();
		foreach($words as $item)
			$wordArray[] = $item['word'];

		if(count($wordArray) < 0)
			return;

		$this->beginTx();

		$wordIDs = $this->getWordIDs($wordArray);

		foreach($words as $item)
		{
			$stmt = $this->sdb->prepare('INSERT INTO [index]([wordid],[itemid],[count]) VALUES(:wordid,:itemid,:count)');
			$stmt->bindValue(':wordid', $wordIDs[ $item['word'] ], SQLITE3_INTEGER);
			$stmt->bindValue(':itemid', $itemID, SQLITE3_INTEGER);
			$stmt->bindValue(':count', $item['count'], SQLITE3_INTEGER);
			$stmt->execute();
		}

		$this->endTx();
	}

	/**
	 * begin DB transaction
	 *
	 */
	public function beginTx($immediate = false)
	{
		$result = true;
		if($this->txCounter == 0)
		{
			$result = $this->sdb->query('BEGIN '.($immediate?'IMMEDIATE ':'').'TRANSACTION');
		}
		++$this->txCounter;
		return $result;
	}

	/**
	 * end DB transaction
	 *
	 */
	public function endTx()
	{
		$result = true;
		if(--$this->txCounter == 0)
		{
			$result = $this->sdb->Query('COMMIT');
		}
		if($this->txCounter < 0)
			$this->txCounter = 0;
		return $result;
	}

	/**
	 * get word IDs, add words if necessary
	 *
	 * @param array $words
	 * @return array
	 */
	private function getWordIDs($words)
	{
		$result = array();

		if(count($words) > 0)
		{
			// find existing words
			$list = array();
			foreach($words as $word)
				$list[] = $this->sdb->escapeString($word);
			$list = '\'' . implode('\',\'', $list) . '\'';

			$res = $this->sdb->query('SELECT [wordid],[word] FROM [word] WHERE [word] IN (' . $list . ')');
			while($row = $res->fetchArray())
			{
				$result[ $row['word'] ] = $row['wordid'];
			}
			$res->finalize();

			// add non-existing words
			foreach($words as $word)
			{
				if(isset($result[$word]))
					continue;

				$stmt = $this->sdb->prepare('INSERT INTO [word]([word]) VALUES(:word)');
				$stmt->bindValue(':word', $word, SQLITE3_TEXT);
				$stmt->execute();

				$result[$word] = $this->sdb->lastInsertRowID();
			}
		}

		return($result);
	}

	/**
	 * optimize the search index, i.e. remove unused words and vacuum database
	 *
	 */
	public function optimize()
	{
		if($this->userID != 18)
			return;

		// remove unused words
		$this->sdb->query('DELETE FROM [word] WHERE [wordid] IN '
			. '(SELECT DISTINCT([word].[wordid]) FROM [word] LEFT JOIN [index] ON [index].[wordid]=[word].[wordid] WHERE [index].[wordid] IS NULL)');

		// rebuild DB
		$this->sdb->query('VACUUM');
	}

	/**
	 * ensure that all tables/indexes exist
	 *
	 */
	private function initDB()
	{
		$this->sdb->busyTimeout(15000);

		$this->sdb->query('CREATE TABLE IF NOT EXISTS [word] ('
			. '	[wordid] INTEGER PRIMARY KEY,'
			. '	[word] TEXT'
			. ')');
		$this->sdb->query('CREATE TABLE IF NOT EXISTS [index] ('
			. '	[indexid] INTEGER PRIMARY KEY,'
			. '	[wordid] INTEGER,'
			. '	[itemid] INTEGER,'
			. '	[count] INTEGER'
			. ')');
		$this->sdb->query('CREATE TABLE IF NOT EXISTS [text] ('
			. '	[itemid] INTEGER,'
			. '	[text] TEXT,'
			. '	PRIMARY KEY([itemid])'
			. ')');

		$this->sdb->query('CREATE INDEX IF NOT EXISTS [index_word] ON [word]([word])');
		$this->sdb->query('CREATE INDEX IF NOT EXISTS [index_wordid] ON [index]([wordid])');
		$this->sdb->query('CREATE INDEX IF NOT EXISTS [index_itemid] ON [index]([itemid])');
	}

	/**
	 * get index db file name
	 *
	 * @return string
	 */
	private function getDBFileName()
	{
		return(DataFilename($this->userID, 'idx'));
	}
}
