<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Regex Helper
 * Standalone helper functions for text cleaning using regex patterns
 */

if (! function_exists('remove_special_chars')) {
	/**
	 * Remove all non-ASCII characters from a string
	 *
	 * @param  string $text
	 * @return string
	 */
	function remove_special_chars($text)
	{
		return preg_replace('/[^\x00-\x7F]+/', '', $text);
	}
}

if (! function_exists('keep_alphanumeric')) {
	/**
	 * Keep only alphanumeric characters and spaces, trim whitespace
	 *
	 * @param  string $text
	 * @return string
	 */
	function keep_alphanumeric($text)
	{
		$text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
		return trim($text);
	}
}

if (! function_exists('keep_printable_ascii')) {
	/**
	 * Keep only printable ASCII characters (32-126) + tab + newline
	 *
	 * @param  string $text
	 * @return string
	 */
	function keep_printable_ascii($text)
	{
		return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $text);
	}
}

if (! function_exists('remove_non_printable')) {
	/**
	 * Remove non-printable control characters (keep printable + whitespace)
	 *
	 * @param  string $text
	 * @return string
	 */
	function remove_non_printable($text)
	{
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
	}
}

if (! function_exists('count_removed_chars')) {
	/**
	 * Count how many characters were removed
	 *
	 * @param  string $before
	 * @param  string $after
	 * @return int
	 */
	function count_removed_chars($before, $after)
	{
		return mb_strlen($before) - mb_strlen($after);
	}
}

if (! function_exists('clean_text')) {
	function clean_text($text, $mode = 'ascii_only')
	{
		$char_before = mb_strlen($text);
		$output      = '';

		switch ($mode)
		{
			case 'ascii_only':
				// Keep all ASCII characters 0-127 (including punctuation)
				$output = preg_replace('/[^\x00-\x7F]+/', '', $text);
				break;

			case 'remove_special':
				$output = preg_replace('/[^a-zA-Z0-9\s\.,!?\-_\(\):;\'\"\/\\\\@#\$%&\*\+\=]/', '', $text);
				break;

			case 'alphanumeric':
				$output = keep_alphanumeric($text);
				break;

			case 'printable':
				$output = keep_printable_ascii($text);
				break;

			default:
				$output = preg_replace('/[^\x00-\x7F]+/', '', $text);
				break;
		}

		$output = normalize_line_endings($output);

		return [
			'output'       => $output,
			'char_before'  => $char_before,
			'char_after'   => mb_strlen($output),
		];
	}
}

if (! function_exists('normalize_line_endings')) {
	function normalize_line_endings($text)
	{
		return preg_replace('/\r\n|\r/', "\n", $text);
	}
}
