<?php

	/**
	 * Text Helper
	 *
	 **/
	Class Text {

		/**
		 * Limits a Text to a number of words
		 *
		 * Example:
		 *   Text::wordLimit('some words to say', 2);
		 *   // some words...
		 *
		 * @see Text::textLimit() for a more powerful version
		 *
		 * @param        $text
		 * @param        $limit
		 * @param string $endCharacter
		 * @return string
		 */
		public static function wordLimit($text, $limit = 16, $endCharacter = '&#8230;') {
			return static::textLimit($text, array(
				'wordLimit' => $limit,
				'endCharacter' => $endCharacter
			));
		}

		/**
		 * Limits a Text to a number of characters
		 *
		 * Example:
		 *   Text::characterLimit('some words to say', 7);
		 *   // some wo...
		 *
		 * @see Text::textLimit() for a more powerful version
		 *
		 * @param        $text
		 * @param int    $limit
		 * @param bool   $preserveWords
		 * @param string $endCharacter
		 * @return string
		 */
		public static function characterLimit($text, $limit = 150, $preserveWords = false, $endCharacter = '&#8230;') {
			return static::textLimit($text, array(
				'characterLimit' => $limit,
				'preserveWords' => $preserveWords,
				'endCharacter' => $endCharacter
			));
		}

		/**
		 * Limits a text to number of characters or words
		 *
		 * Examples:
		 *   Text::textLimit('some words to say', array('characterLimit' => 7));
		 *   // some wo...
		 *   Text::textLimit('some words to say', array('characterLimit' => 7, 'preserveWords' => true));
		 *   // some words...
		 *   Text::textLimit('some words to say', array('wordLimit' => 3));
		 *   // some words to...
		 *   Text::textLimit('some words to say', array('characterLimit' => 7, 'wordLimit' => 3));
		 *   // some wo...
		 *   Text::textLimit('a b c is my tee', array('characterLimit' => 7, 'wordLimit' => 3));
		 *   // a b c...
		 *
		 *
		 * @param       $text
		 * @param array $options
		 *   'wordLimit': After x words cut the string (can be combined with characterLimit)
		 *   'characterLimit': After x characters cut the string (can be combined with characterLimit)
		 *   'endCharacter': Character to add after string has been cut (defaults to &#8230; e.g. ...)
		 *   'preserveWords': Cut only afters a word even if the *characterLimit* is reached.
		 * @return string
		 */
		public static function textLimit($text, $options = array()) {
			$wordLimit = isset($options['wordLimit']) ? $options['wordLimit'] : false;
			$characterLimit = isset($options['characterLimit']) ? $options['characterLimit'] : false;
			$endCharacter = isset($options['endCharacter']) ? $options['endCharacter'] : '&#8230;';
			$preserveWords = isset($options['preserveWords']) ? $options['preserveWords'] : false;

			$textArray = str_split($text);
			$wordCount = 0;
			$return = false;
			foreach($textArray as $i => $character) {
				if ($characterLimit !== false && $i >= $characterLimit) {
					if (!$preserveWords || $character === ' ') {
						$return = true;
					}
				}

				if ($character === ' ') {
					$wordCount++;
				}
				if ($wordLimit !== false && $wordCount >= $wordLimit) {
					$return = true;
				}

				if ($return === true) {
					return trim(mb_substr($text, 0, $i) . $endCharacter);
				}
			}
			return $text;
		}

		/**
		 * Translates a camel case string into a string separated by a character
		 *
		 * Examples
		 *   Text::camelCaseToCharSeparated('FirstName');
		 *   // first-name
		 *   Text::camelCaseToCharSeparated('TextArea', ' ');
		 *   // text area
		 *   Text::camelCaseToCharSeparated('BlogPost', ' ', false);
		 *   // Blog Post
		 *
		 * @param string $camelCase String in camel case format
		 * @param string $char
		 * @param bool   $toLowerCase
		 * @return string $str Translated into underscore format
		 */
		public static function camelCaseToCharSeparated($camelCase, $char = '-', $toLowerCase = true) {
			if ($toLowerCase === true) {
				$func = create_function('$c', 'return "' . $char . '" . strtolower($c[1]);');
				return preg_replace_callback('/([A-Z])/', $func, lcfirst($camelCase));
			} else {
				$func = create_function('$c', 'return "' . $char . '" . $c[1];');
				$result = preg_replace_callback('/([A-Z])/', $func, ucfirst($camelCase));
				return substr($result, 1);
			}
		}

		/**
		 * Translates a camel case path into a dot notation
		 *
		 * Examples
		 *   Text::camelCasePathToDotNotation('Page/SocialMedia');
		 *   => page.socialMedia
		 *
		 * @param string $camelCase String in camel case format
		 * @return string $str Translated into underscore format
		 */
		public static function camelCasePathToDotNotation($camelCase) {
			$camelCase[0] = strtolower($camelCase[0]);
			$func = create_function('$c', 'return "." . strtolower($c[1]);');
			return preg_replace_callback('#/([A-Z])#', $func, $camelCase);
		}

		/**
		 * Parses a "Lorem Ipsum" text, using the API from http://loripsum.net/
		 *
		 * You can add extra parameters to specify the output you're going to get. Say, you need 10 short paragraphs with headings, use 10/short/headers. All of the possible parameters are:
		 * - (integer) - The number of paragraphs to generate.
		 * - short, medium, long, verylong - The average length of a paragraph.
		 * - decorate - Add bold, italic and marked text.
		 * - link - Add links.
		 * - ul - Add unordered lists.
		 * - ol - Add numbered lists.
		 * - dl - Add description lists.
		 * - bq - Add blockquotes.
		 * - code - Add code samples.
		 * - headers - Add headers.
		 * - allcaps - Use ALL CAPS.
		 * - prude - Prude version.
		 * - plaintext - Return plain text, no HTML.
		 *
		 * Example:
		 *   Text::loremIpsum();
		 * Returns:
		 *   Lorem ipsum dolor sit amet, consectetur adipiscing elit. Eaedem res maneant alio modo. Haeret in salebra. Quo modo? Conferam avum tuum Drusum cum C. Tum mihi Piso: Quid ergo? Eam stabilem appellas.
		 *
		 * Examples:
		 *   Text::loremIpsum('5/link/bq/long');
		 *   Text::loremIpsum('3/ul/medium');
		 *   Text::loremIpsum('3/headers/link/ul/long');
		 *
		 * @todo improvement: don’t use slow web api, consider using Faker (https://github.com/fzaninotto/Faker)
		 *
		 * @param string $api
		 * @return string
		 */
		public static function loremIpsum($api = '1/short/plaintext') {
			$text = trim(file_get_contents('http://loripsum.net/api/'.$api));
			$pos = strpos($text, '.');
			return substr($text, $pos+1);
		}

		/**
		 * Converts your numeric value into its textual value.
		 * Note: float rounding can be avoided by passing the number as a string
		 *
		 * Examples:
		 *   Text::numberToWords(2);
		 *   => two
		 *
		 *   Text::numberToWords(-1922685.477);
		 *   => negative one million, nine hundred and twenty-two thousand, six hundred and eighty-five point four seven seven
		 *
		 *   Text::numberToWords(789123.12345); // rounds the fractional part
		 *   => seven hundred and eighty-nine thousand, one hundred and twenty-three point one two
		 *
		 *   Text::numberToWords('789123.12345'); // does not round
		 *   => seven hundred and eighty-nine thousand, one hundred and twenty-three point one two three four five
		 *
		 * based on a version from Karl on July 18, 2011
		 * @link http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
		 * @param       $number
		 * @param array $options
		 * @return bool|string
		 */
		public static function numberToWords($number, $options = array()) {
			$options['hyphen']      = isset($options['hyphen'])      ? $options['hyphen']      : '-';
			$options['conjunction'] = isset($options['conjunction']) ? $options['conjunction'] : ' and ';
			$options['separator']   = isset($options['separator'])   ? $options['separator']   : ', ';
			$options['negative']    = isset($options['negative'])    ? $options['negative']    : 'negative ';
			$options['decimal']     = isset($options['decimal'])     ? $options['decimal']     : ' point ';
			$options['dictionary']  = isset($options['dictionary'])  ? $options['dictionary']  : array(
				0 => 'zero',
				1 => 'one',
				2 => 'two',
				3 => 'three',
				4 => 'four',
				5 => 'five',
				6 => 'six',
				7 => 'seven',
				8 => 'eight',
				9 => 'nine',
				10 => 'ten',
				11 => 'eleven',
				12 => 'twelve',
				13 => 'thirteen',
				14 => 'fourteen',
				15 => 'fifteen',
				16 => 'sixteen',
				17 => 'seventeen',
				18 => 'eighteen',
				19 => 'nineteen',
				20 => 'twenty',
				30 => 'thirty',
				40 => 'fourty',
				50 => 'fifty',
				60 => 'sixty',
				70 => 'seventy',
				80 => 'eighty',
				90 => 'ninety',
				100 => 'hundred',
				1000 => 'thousand',
				1000000 => 'million',
				1000000000 => 'billion',
				1000000000000 => 'trillion',
				1000000000000000 => 'quadrillion',
				1000000000000000000 => 'quintillion'
			);

			if (!is_numeric($number)) {
				return false;
			}

			if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
				// overflow
				trigger_error(
					'static::numberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
					E_USER_WARNING
				);
				return false;
			}

			if ($number < 0) {
				return $options['negative'] . static::numberToWords(abs($number));
			}

			$fraction = null;
			if (strpos($number, '.') !== false) {
				list($number, $fraction) = explode('.', $number);
			}

			switch (true) {
				case $number < 21:
					$string = $options['dictionary'][$number];
					break;
				case $number < 100:
					$tens   = ((int) ($number / 10)) * 10;
					$units  = $number % 10;
					$string = $options['dictionary'][$tens];
					if ($units) {
						$string .= $options['hyphen'] . $options['dictionary'][$units];
					}
					break;
				case $number < 1000:
					$hundreds  = (int) floor($number / 100);
					$remainder = $number % 100;
					$string = $options['dictionary'][$hundreds] . ' ' . $options['dictionary'][100];
					if ($remainder) {
						$string .= $options['conjunction'] . static::numberToWords($remainder);
					}
					break;
				default:
					$baseUnit = pow(1000, floor(log($number, 1000)));
					$numBaseUnits = (int) ($number / $baseUnit);
					$remainder = $number % $baseUnit;
					$string = static::numberToWords($numBaseUnits) . ' ' . $options['dictionary'][$baseUnit];
					if ($remainder) {
						$string .= $remainder < 100 ? $options['conjunction'] : $options['separator'];
						$string .= static::numberToWords($remainder);
					}
					break;
			}

			if (null !== $fraction && is_numeric($fraction)) {
				$string .= $options['decimal'];
				$words = array();
				foreach (str_split((string) $fraction) as $number) {
					$words[] = $options['dictionary'][$number];
				}
				$string .= implode(' ', $words);
			}

			return $string;
		}

		/**
		 * Returns a cleaned version of a human readable number
		 *
		 * Examples:
		 *   Text::getCleanPhoneNumber('+43 123 / 456789');
		 *   => 0043123456789
		 *
		 *   Text::getCleanPhoneNumber('123 / 456789');
		 *   => 0043123456789
		 *
		 *   Text::getCleanPhoneNumber('123 / 456789', 001);
		 *   => 001123456789
		 *
		 * @param        $number
		 * @param string $defaultCountry
		 * @return mixed|string
		 */
		public static function getCleanPhoneNumber($number, $defaultCountry = '0043') {
			if (substr($number, 0, 2) === '00' || $number[0] === '+') {
				$cleanNumber = $number;
			} else {
				if (substr($number, 0, 1) === '0') {
					$cleanNumber = $defaultCountry . substr($number, 1);
				} else {
					$cleanNumber = $defaultCountry . $number;
				}
			}
			$cleanNumber = str_replace('+', '00', $cleanNumber);
			$cleanNumber = str_replace(array('(', ')', ' ', '-', '/', '.'), '', $cleanNumber);
			return $cleanNumber;
		}

		/**
		 * Return a Human Readable File Size
		 *
		 * Example:
		 *   Text::humanFileSize('path/to/your/file');
		 *   => 3.25 MB
		 *
		 *   Text::humanFileSize(get_attached_file($file['ID']));
		 *   => 2.45 kB
		 *
		 * @param mixed    $input     FilePath or Size in Bytes
		 * @param int      $decimals
		 * @return string
		 */
		public static function humanFileSize($input, $decimals = 2) {
			$bytes = is_string($input) && is_file($input) ? filesize($input) : $input;
			$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
			$factor = (int) floor((strlen($bytes) - 1) / 3);
			return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
		}

		/**
		 * This is a copy from WordPress!!
		 *
		 * Sanitizes a title, replacing whitespace and a few other characters with dashes.
		 *
		 * Limits the output to alphanumeric characters, underscore (_) and dash (-).
		 * Whitespace becomes a dash.
		 *
		 *
		 * @param string $title     The title to be sanitized.
		 * @return string The sanitized title.
		 */
		public static function sanitizeTitle( $title ) {
			$title = strip_tags($title);
			// Preserve escaped octets.
			$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
			// Remove percent signs that are not part of an octet.
			$title = str_replace('%', '', $title);
			// Restore octets.
			$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

//			if (seems_utf8($title)) {
//				if (function_exists('mb_strtolower')) {
//					$title = mb_strtolower($title, 'UTF-8');
//				}
//				$title = utf8_uri_encode($title, 200);
//			}

			$title = strtolower($title);

			// Convert nbsp, ndash and mdash to hyphens
			$title = str_replace( array( '%c2%a0', '%e2%80%93', '%e2%80%94' ), '-', $title );
			// Convert nbsp, ndash and mdash HTML entities to hyphens
			$title = str_replace( array( '&nbsp;', '&#160;', '&ndash;', '&#8211;', '&mdash;', '&#8212;' ), '-', $title );

			// Strip these characters entirely
			$title = str_replace( array(
				// iexcl and iquest
				'%c2%a1', '%c2%bf',
				// angle quotes
				'%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
				// curly quotes
				'%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
				'%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
				// copy, reg, deg, hellip and trade
				'%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
				// acute accents
				'%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
				// grave accent, macron, caron
				'%cc%80', '%cc%84', '%cc%8c',
			), '', $title );

			// Convert times to x
			$title = str_replace( '%c3%97', 'x', $title );

			$title = preg_replace('/&.+?;/', '', $title); // kill entities
			$title = str_replace('.', '-', $title);

			$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
			$title = preg_replace('/\s+/', '-', $title);
			$title = preg_replace('|-+|', '-', $title);
			$title = trim($title, '-');

			return $title;
		}

	} // end Helper