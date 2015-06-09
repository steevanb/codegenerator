<?php

namespace steevanb\CodeGenerator\PHP;

/**
 * Tools for PHPDoc
 */
class PHPDoc
{
	/**
	 * Parse a PHPDoc bloc
	 *
	 * @param string $phpDoc
	 * @return array
	 */
	public static function parse($phpDoc)
	{
		$return = array(
			'comments' => array()
		);
		if (is_string($phpDoc) === false) {
			return $return;
		}

		$lines = explode("\n", str_replace("\r", null, $phpDoc));
		foreach ($lines as $line) {
			$line = trim($line);

			// start or end code
			if ($line == '/**' || $line == '*/') {
				continue;
			}

			// delete '* '
			$line = trim(substr($line, 1));

			// comment
			if (substr($line, 0, 1) != '@') {
				$return['comments'][] = $line;
			} else {
				$keyword = (strpos($line, ' ') === false) ? $line : substr($line, 0, strpos($line, ' '));
				$comment = trim(substr($line, strlen($keyword)));

				// search for comment parts
				switch ($keyword) {
					case '@var' :
						$parts = self::explodeParts($comment, 2);
						$commentParts = array(
							'type' => $parts[0],
							'comment' => $parts[1]
						);
						break;
					default:
						$commentParts = array('comment' => $comment);
						break;
				}

				if (array_key_exists($keyword, $return) === false) {
					$return[$keyword] = array();
				}
				$return[$keyword][] = $commentParts;
			}
		}

		return $return;
	}

	/**
	 * Explode phpdoc line parts
	 *
	 * @param string $comment Comment line, without keyword
	 * @param int $count Number of parts you want
	 * @param string $separator
	 * @return array
	 */
	public static function explodeParts($comment, $count, $separator = ' ')
	{
		$return = array();
		for ($i = 0; $i < $count; $i++) {
			$return[] = null;
		}

		$i = 0;
		while (strlen($comment) > 0) {
			// last item
			if ($i == count($return) - 1) {
				$return[$i] = $comment;
				break;
			}

			$posSeparator = strpos($comment, $separator);
			if ($posSeparator === false) {
				$return[$i] = $comment;
				break;
			} else {
				$return[$i] = trim(substr($comment, 0, $posSeparator));
				$comment = substr($comment, $posSeparator + 1);
			}

			$i++;
		}

		return $return;
	}

	/**
	 * Generate a PHPDoc
	 *
	 * @param array $phpDoc
	 * @return array
	 */
	public static function generate(array $phpDoc)
	{
		$start = ' * ';

		// comment
		$comments = array();
		if (array_key_exists('comments', $phpDoc)) {
			foreach ($phpDoc['comments'] as $line) {
				$comments[] = $start . $line;
			}
			unset($phpDoc['comments']);
		}

		// keywords
		$keywords = array();
		foreach ($phpDoc as $keyword => $doc) {
			switch ($keyword) {
				case '@var' :
				case '@return' :
					$comment = $start . $keyword . ' ' . $doc['type'];
					if (array_key_exists('comment', $doc) && $doc['comment'] != null) {
						$comment .= ' ' . $doc['comment'];
					}
					$keywords[] = $comment;
					break;
				default:

					break;
			}
		}

		// no phpdoc to generate
		if (count($comments) == 0 && count($keywords) == 0) {
			return array();
		}

		// generate full phpdoc
		$return = array('/**');
		if (count($comments) > 0) {
			$return = array_merge($return, $comments);
			if (count($keywords) > 0) {
				$return[] = $start;
			}
		}
		$return = array_merge($return, $keywords);
		$return[] = ' */';

		return $return;
	}
}