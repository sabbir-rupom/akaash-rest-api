<?php

(defined('APP_NAME')) or exit('Forbidden 403');

namespace Akaash\System;

/**
 * Security class for Akaash REST-template
 * Most class properties and methods are used from CodeIgniter 3.1+ Security class
 *
 * @link       https://codeigniter.com/user_guide/libraries/security.html
 */
class Security
{

    /**
     * List of never allowed strings
     * @var array
     */
    protected static $never_allowed_str = array(
      'document.cookie' => '[removed]',
      '(document).cookie' => '[removed]',
      'document.write' => '[removed]',
      '(document).write' => '[removed]',
      '.parentNode' => '[removed]',
      '.innerHTML' => '[removed]',
      '-moz-binding' => '[removed]',
      '<!--' => '&lt;!--',
      '-->' => '--&gt;',
      '<![CDATA[' => '&lt;![CDATA[',
      '<comment>' => '&lt;comment&gt;',
      '<%' => '&lt;&#37;'
    );

    /**
     * List of never allowed regex replacements
     *
     * @var array
     */
    protected static $never_allowed_regex = array(
      'javascript\s*:',
      '(\(?document\)?|\(?window\)?(\.document)?)\.(location|on\w*)',
      'expression\s*(\(|&\#40;)', // CSS and IE
      'vbscript\s*:', // IE, surprise!
      'wscript\s*:', // IE
      'jscript\s*:', // IE
      'vbs\s*:', // IE
      'Redirect\s+30\d',
      "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
    );

    /**
     * constructor
     */
    public function __construct()
    {
    }

    /**
     * XSS Clean
     *
     * Sanitizes data so that Cross Site Scripting Hacks can be
     * prevented.  This method is not 100% foolproof,
     * source suggestions are picked from CodeIgniter Security class
     *
     * @param   string|string[] $str  Input data
     * @return  string
     */
    public static function xssClean($str, $is_image = false)
    {
        // Is the string an array?
        if (is_array($str)) {
            foreach ($str as $key => &$value) {
                $str[$key] = self::xssClean($value);
            }

            return $str;
        }

        // Remove Invisible Characters
        $str = self::removeInvisibleCharacters($str);

        /*
         * URL Decode
         * Just in case stuff like this is submitted:
         *
         * <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a>
         *
         * Note: Use rawurldecode() so it does not remove plus signs
         */
        if (stripos($str, '%') !== false) {
            do {
                $oldstr = $str;
                $str = rawurldecode($str);
                $str = preg_replace_callback('#%(?:\s*[0-9a-f]){2,}#i', [self::class, 'urldecodespaces'], $str);
            } while ($oldstr !== $str);
            unset($oldstr);
        }

        /*
         * Convert all tabs to spaces
         *
         * This prevents strings like this: javascript
         * NOTE: we deal with spaces between characters later.
         * NOTE: preg_replace was found to be amazingly slow here on
         * large blocks of data, so we use str_replace.
         */
        $str = str_replace("\t", ' ', $str);

        // Capture converted string for later comparison
        $converted_string = $str;

        // Remove Strings that are never allowed
        $str = self::doNeverAllowed($str);

        /*
         * Makes PHP tags safe
         *
         * Note: XML tags are inadvertently replaced too:
         *
         * <?xml
         *
         * But it doesn't seem to pose a problem.
         */
        $str = str_replace(array('<?', '?' . '>'), array('&lt;?', '?&gt;'), $str);

        /*
         * Compact any exploded words
         *
         * This corrects words like:  j a v a s c r i p t
         * These words are compacted back to their correct state.
         */
        $words = array(
          'javascript', 'expression', 'vbscript', 'jscript', 'wscript',
          'vbs', 'script', 'base64', 'applet', 'alert', 'document',
          'write', 'cookie', 'window', 'confirm', 'prompt', 'eval'
        );

        foreach ($words as $word) {
            $word = implode('\s*', str_split($word)) . '\s*';

            // We only want to do this when it is followed by a non-word character
            // That way valid stuff like "dealer to" does not become "dealerto"
            $str = preg_replace_callback(
                '#(' . substr($word, 0, -3) . ')(\W)#is',
                [self::class, 'compactExplodedWords'],
                $str
            );
        }

        /*
         * Remove disallowed Javascript in links or img tags
         * We used to do some version comparisons and use of stripos(),
         * but it is dog slow compared to these simplified non-capturing
         * preg_match(), especially if the pattern exists in the string
         *
         * Note: It was reported that not only space characters, but all in
         * the following pattern can be parsed as separators between a tag name
         * and its attributes: [\d\s"\'`;,\/\=\(\x00\x0B\x09\x0C]
         * ... however, removeInvisibleCharacters() above already strips the
         * hex-encoded ones, so we'll skip them below.
         */
        do {
            $original = $str;

            if (preg_match('/<a/i', $str)) {
                $str = preg_replace_callback(
                    '#<a(?:rea)?[^a-z0-9>]+([^>]*?)(?:>|$)#si',
                    [self::class, 'jsLinkRemoval'],
                    $str
                );
            }

            if (preg_match('/<img/i', $str)) {
                $str = preg_replace_callback(
                    '#<img[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#si',
                    [self::class, 'jsImgRemoval'],
                    $str
                );
            }

            if (preg_match('/script|xss/i', $str)) {
                $str = preg_replace('#</*(?:script|xss).*?>#si', '[removed]', $str);
            }
        } while ($original !== $str);
        unset($original);

        /*
         * Sanitize naughty HTML elements
         *
         * If a tag containing any of the words in the list
         * below is found, the tag gets converted to entities.
         *
         * So this: <blink>
         * Becomes: &lt;blink&gt;
         */
        $pattern = '#'
            // tag start and name, followed by a non-tag character
            . '<((?<slash>/*\s*)((?<tagName>[a-z0-9]+)(?=[^a-z0-9]|$)|.+)'
            // a valid attribute character immediately after the tag would count as a separator
            . '[^\s\042\047a-z0-9>/=]*'
            // non-attribute characters, excluding > (tag close) for obvious reasons
            . '(?<attributes>(?:[\s\042\047/=]*'
            . '[^\s\042\047>/=]+' // attribute characters
            // optional attribute-value
            . '(?:\s*=' // attribute-value separator
            // single, double or non-quoted value
            . '(?:[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*))'
            . ')?' // end optional attribute-value group
            . ')*)' // end optional attributes group
            . '[^>]*)(?<closeTag>\>)?#isS';

        // Note: It would be nice to optimize this for speed, BUT
        //       only matching the naughty elements here results in
        //       false positives and in turn - vulnerabilities!
        do {
            $old_str = $str;
            $str = preg_replace_callback($pattern, [self::class, 'sanitizeNaughtyHtml'], $str);
        } while ($old_str !== $str);
        unset($old_str);

        /*
         * Sanitize naughty scripting elements
         *
         * Similar to above, only instead of looking for
         * tags it looks for PHP and JavaScript commands
         * that are disallowed. Rather than removing the
         * code, it simply converts the parenthesis to entities
         * rendering the code un-executable.
         *
         * For example: eval('some code')
         * Becomes:     eval&#40;'some code'&#41;
         */
        $str = preg_replace(
            '#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system'
            . '|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
            '\\1\\2&#40;\\3&#41;',
            $str
        );

        // Same thing, but for "tag functions" (e.g. eval`some code`)
        // See https://github.com/bcit-ci/CodeIgniter/issues/5420
        $str = preg_replace(
            '#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system'
            . '|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)`(.*?)`#si',
            '\\1\\2&#96;\\3&#96;',
            $str
        );

        // Final clean up
        // This adds a bit of extra precaution in case
        // something got through the above filters
        $str = self::doNeverAllowed($str);

        return $str;
    }

    /**
     * Remove Invisible Characters
     *
     * This prevents sandwiching null characters
     * between ascii characters, like Java\0script.
     *
     * @param   string
     * @param   bool
     * @return  string
     */
    protected static function removeInvisibleCharacters($str, $url_encoded = true)
    {
        $non_displayables = array();

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/i'; // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/i'; // url encoded 16-31
            $non_displayables[] = '/%7f/i'; // url encoded 127
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }

    /**
     * Do Never Allowed
     *
     * @param   string
     * @return  string
     */
    protected static function doNeverAllowed($str)
    {
        $str = str_replace(array_keys(self::$never_allowed_str), self::$never_allowed_str, $str);

        foreach (self::$never_allowed_regex as $regex) {
            $str = preg_replace('#' . $regex . '#is', '[removed]', $str);
        }

        return $str;
    }

    /**
     * URL-decode taking spaces into account
     *
     * @param   array   $matches
     * @return  string
     */
    protected static function urldecodespaces($matches)
    {
        $input = $matches[0];
        $nospaces = preg_replace('#\s+#', '', $input);
        return ($nospaces === $input) ? $input : rawurldecode($nospaces);
    }

    /**
     * Compact Exploded Words
     *
     * Callback method for xssClean() to remove whitespace from
     * things like 'javascript'.
     *
     * @param   array  $matches
     * @return  string
     */
    protected static function compactExplodedWords($matches)
    {
        return preg_replace('/\s+/s', '', $matches[1]) . $matches[2];
    }

    /**
     * JS Link Removal
     *
     * Callback method for xssClean() to sanitize links.
     *
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on link-heavy strings.
     *
     * @param   array  $match
     * @return  string
     */
    protected static function jsLinkRemoval($match)
    {
        return str_replace(
            $match[1],
            preg_replace(
                '#href=.*?(?:(?:alert|prompt|confirm)(?:\(|&\#40;|`|&\#96;)'
                . '|javascript:|livescript:|mocha:|charset=|window\.|\(?document\)?\.|\.cookie'
                . '|<script|<xss|d\s*a\s*t\s*a\s*:)#si',
                '',
                $this->_filter_attributes($match[1])
            ),
            $match[0]
        );
    }

    /**
     * JS Image Removal
     *
     * Callback method for xssClean() to sanitize image tags.
     *
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on image tag heavy strings.
     *
     * @param   array  $match
     * @return  string
     */
    protected static function jsImgRemoval($match)
    {
        return str_replace(
            $match[1],
            preg_replace(
                '#src=.*?(?:(?:alert|prompt|confirm|eval)(?:\(|&\#40;|`|&\#96;)'
                . '|javascript:|livescript:|mocha:|charset=|window\.|\(?document\)'
                . '?\.|\.cookie|<script|<xss|base64\s*,)#si',
                '',
                $this->_filter_attributes($match[1])
            ),
            $match[0]
        );
    }

    /**
     * Sanitize Naughty HTML
     *
     * Callback method for xssClean() to remove naughty HTML elements.
     *
     * @param   array   $matches
     * @return  string
     */
    protected static function sanitizeNaughtyHtml($matches)
    {
        static $naughty_tags = array(
          'alert', 'area', 'prompt', 'confirm', 'applet', 'audio', 'basefont', 'base', 'behavior', 'bgsound',
          'blink', 'body', 'embed', 'expression', 'form', 'frameset', 'frame', 'head', 'html', 'ilayer',
          'iframe', 'input', 'button', 'select', 'isindex', 'layer', 'link', 'meta', 'keygen', 'object',
          'plaintext', 'style', 'script', 'textarea', 'title', 'math', 'video', 'svg', 'xml', 'xss'
        );

        static $evil_attributes = array(
          'on\w+', 'style', 'xmlns', 'formaction', 'form', 'xlink:href', 'FSCommand', 'seekSegmentTime'
        );

        // First, escape unclosed tags
        if (empty($matches['closeTag'])) {
            return '&lt;' . $matches[1];
        } elseif (in_array(strtolower($matches['tagName']), $naughty_tags, true)) {
            // Is the element that we caught naughty? If so, escape it
            return '&lt;' . $matches[1] . '&gt;';
        } elseif (isset($matches['attributes'])) {
            // For other tags, see if their attributes are "evil" and strip those
            // We'll store the already filtered attributes here
            $attributes = array();

            // Attribute-catching pattern
            $attributes_pattern = '#'
                . '(?<name>[^\s\042\047>/=]+)' // attribute characters
                // optional attribute-value ( separator )
                . '(?:\s*=(?<value>[^\s\042\047=><`]+|\s*\042[^\042]*\042'
                . '|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*)))'
                . '#i';

            // Blacklist pattern for evil attribute names
            $is_evil_pattern = '#^(' . implode('|', $evil_attributes) . ')$#i';

            // Each iteration filters a single attribute
            do {
                // Strip any non-alpha characters that may precede an attribute.
                // Browsers often parse these incorrectly and that has been a
                // of numerous XSS issues we've had.
                $matches['attributes'] = preg_replace('#^[^a-z]+#i', '', $matches['attributes']);

                if (!preg_match($attributes_pattern, $matches['attributes'], $attribute, PREG_OFFSET_CAPTURE)) {
                    // No (valid) attribute found? Discard everything else inside the tag
                    break;
                }

                if (
                // Is it indeed an "evil" attribute?
                    preg_match($is_evil_pattern, $attribute['name'][0])
                    // Or does it have an equals sign, but no value and not quoted? Strip that too!
                    or (trim($attribute['value'][0]) === '')
                ) {
                    $attributes[] = 'xss=removed';
                } else {
                    $attributes[] = $attribute[0][0];
                }

                $matches['attributes'] = substr($matches['attributes'], $attribute[0][1] + strlen($attribute[0][0]));
            } while ($matches['attributes'] !== '');

            $attributes = empty($attributes) ? '' : ' ' . implode(' ', $attributes);
            return '<' . $matches['slash'] . $matches['tagName'] . $attributes . '>';
        }

        return $matches[0];
    }
}
