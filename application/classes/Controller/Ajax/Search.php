<?php defined('SYSPATH') or die('No direct script access.');

/*====================================
* More code come from Drupal 7.31
====================================*/

/**
 * Matches Unicode characters that are word boundaries.
 *
 * Characters with the following General_category (gc) property values are used
 * as word boundaries. While this does not fully conform to the Word Boundaries
 * algorithm described in http://unicode.org/reports/tr29, as PCRE does not
 * contain the Word_Break property table, this simpler algorithm has to do.
 * - Cc, Cf, Cn, Co, Cs: Other.
 * - Pc, Pd, Pe, Pf, Pi, Po, Ps: Punctuation.
 * - Sc, Sk, Sm, So: Symbols.
 * - Zl, Zp, Zs: Separators.
 *
 * Non-boundary characters include the following General_category (gc) property
 * values:
 * - Ll, Lm, Lo, Lt, Lu: Letters.
 * - Mc, Me, Mn: Combining Marks.
 * - Nd, Nl, No: Numbers.
 *
 * Note that the PCRE property matcher is not used because we wanted to be
 * compatible with Unicode 5.2.0 regardless of the PCRE version used (and any
 * bugs in PCRE property tables).
 *
 * @see http://unicode.org/glossary
 */
define('PREG_CLASS_UNICODE_WORD_BOUNDARY',
  '\x{0}-\x{2F}\x{3A}-\x{40}\x{5B}-\x{60}\x{7B}-\x{A9}\x{AB}-\x{B1}\x{B4}' .
  '\x{B6}-\x{B8}\x{BB}\x{BF}\x{D7}\x{F7}\x{2C2}-\x{2C5}\x{2D2}-\x{2DF}' .
  '\x{2E5}-\x{2EB}\x{2ED}\x{2EF}-\x{2FF}\x{375}\x{37E}-\x{385}\x{387}\x{3F6}' .
  '\x{482}\x{55A}-\x{55F}\x{589}-\x{58A}\x{5BE}\x{5C0}\x{5C3}\x{5C6}' .
  '\x{5F3}-\x{60F}\x{61B}-\x{61F}\x{66A}-\x{66D}\x{6D4}\x{6DD}\x{6E9}' .
  '\x{6FD}-\x{6FE}\x{700}-\x{70F}\x{7F6}-\x{7F9}\x{830}-\x{83E}' .
  '\x{964}-\x{965}\x{970}\x{9F2}-\x{9F3}\x{9FA}-\x{9FB}\x{AF1}\x{B70}' .
  '\x{BF3}-\x{BFA}\x{C7F}\x{CF1}-\x{CF2}\x{D79}\x{DF4}\x{E3F}\x{E4F}' .
  '\x{E5A}-\x{E5B}\x{F01}-\x{F17}\x{F1A}-\x{F1F}\x{F34}\x{F36}\x{F38}' .
  '\x{F3A}-\x{F3D}\x{F85}\x{FBE}-\x{FC5}\x{FC7}-\x{FD8}\x{104A}-\x{104F}' .
  '\x{109E}-\x{109F}\x{10FB}\x{1360}-\x{1368}\x{1390}-\x{1399}\x{1400}' .
  '\x{166D}-\x{166E}\x{1680}\x{169B}-\x{169C}\x{16EB}-\x{16ED}' .
  '\x{1735}-\x{1736}\x{17B4}-\x{17B5}\x{17D4}-\x{17D6}\x{17D8}-\x{17DB}' .
  '\x{1800}-\x{180A}\x{180E}\x{1940}-\x{1945}\x{19DE}-\x{19FF}' .
  '\x{1A1E}-\x{1A1F}\x{1AA0}-\x{1AA6}\x{1AA8}-\x{1AAD}\x{1B5A}-\x{1B6A}' .
  '\x{1B74}-\x{1B7C}\x{1C3B}-\x{1C3F}\x{1C7E}-\x{1C7F}\x{1CD3}\x{1FBD}' .
  '\x{1FBF}-\x{1FC1}\x{1FCD}-\x{1FCF}\x{1FDD}-\x{1FDF}\x{1FED}-\x{1FEF}' .
  '\x{1FFD}-\x{206F}\x{207A}-\x{207E}\x{208A}-\x{208E}\x{20A0}-\x{20B8}' .
  '\x{2100}-\x{2101}\x{2103}-\x{2106}\x{2108}-\x{2109}\x{2114}' .
  '\x{2116}-\x{2118}\x{211E}-\x{2123}\x{2125}\x{2127}\x{2129}\x{212E}' .
  '\x{213A}-\x{213B}\x{2140}-\x{2144}\x{214A}-\x{214D}\x{214F}' .
  '\x{2190}-\x{244A}\x{249C}-\x{24E9}\x{2500}-\x{2775}\x{2794}-\x{2B59}' .
  '\x{2CE5}-\x{2CEA}\x{2CF9}-\x{2CFC}\x{2CFE}-\x{2CFF}\x{2E00}-\x{2E2E}' .
  '\x{2E30}-\x{3004}\x{3008}-\x{3020}\x{3030}\x{3036}-\x{3037}' .
  '\x{303D}-\x{303F}\x{309B}-\x{309C}\x{30A0}\x{30FB}\x{3190}-\x{3191}' .
  '\x{3196}-\x{319F}\x{31C0}-\x{31E3}\x{3200}-\x{321E}\x{322A}-\x{3250}' .
  '\x{3260}-\x{327F}\x{328A}-\x{32B0}\x{32C0}-\x{33FF}\x{4DC0}-\x{4DFF}' .
  '\x{A490}-\x{A4C6}\x{A4FE}-\x{A4FF}\x{A60D}-\x{A60F}\x{A673}\x{A67E}' .
  '\x{A6F2}-\x{A716}\x{A720}-\x{A721}\x{A789}-\x{A78A}\x{A828}-\x{A82B}' .
  '\x{A836}-\x{A839}\x{A874}-\x{A877}\x{A8CE}-\x{A8CF}\x{A8F8}-\x{A8FA}' .
  '\x{A92E}-\x{A92F}\x{A95F}\x{A9C1}-\x{A9CD}\x{A9DE}-\x{A9DF}' .
  '\x{AA5C}-\x{AA5F}\x{AA77}-\x{AA79}\x{AADE}-\x{AADF}\x{ABEB}' .
  '\x{E000}-\x{F8FF}\x{FB29}\x{FD3E}-\x{FD3F}\x{FDFC}-\x{FDFD}' .
  '\x{FE10}-\x{FE19}\x{FE30}-\x{FE6B}\x{FEFF}-\x{FF0F}\x{FF1A}-\x{FF20}' .
  '\x{FF3B}-\x{FF40}\x{FF5B}-\x{FF65}\x{FFE0}-\x{FFFD}');

/**
 * Matches CJK (Chinese, Japanese, Korean) letter-like characters.
 *
 * This list is derived from the "East Asian Scripts" section of
 * http://www.unicode.org/charts/index.html, as well as a comment on
 * http://unicode.org/reports/tr11/tr11-11.html listing some character
 * ranges that are reserved for additional CJK ideographs.
 *
 * The character ranges do not include numbers, punctuation, or symbols, since
 * these are handled separately in search. Note that radicals and strokes are
 * considered symbols. (See
 * http://www.unicode.org/Public/UNIDATA/extracted/DerivedGeneralCategory.txt)
 *
 * @see search_expand_cjk()
 */
define('PREG_CLASS_CJK', '\x{1100}-\x{11FF}\x{3040}-\x{309F}\x{30A1}-\x{318E}' .
  '\x{31A0}-\x{31B7}\x{31F0}-\x{31FF}\x{3400}-\x{4DBF}\x{4E00}-\x{9FCF}' .
  '\x{A000}-\x{A48F}\x{A4D0}-\x{A4FD}\x{A960}-\x{A97F}\x{AC00}-\x{D7FF}' .
  '\x{F900}-\x{FAFF}\x{FF21}-\x{FF3A}\x{FF41}-\x{FF5A}\x{FF66}-\x{FFDC}' .
  '\x{20000}-\x{2FFFD}\x{30000}-\x{3FFFD}');

/**
 * Matches all 'N' Unicode character classes (numbers)
 */
define('PREG_CLASS_NUMBERS',
  '\x{30}-\x{39}\x{b2}\x{b3}\x{b9}\x{bc}-\x{be}\x{660}-\x{669}\x{6f0}-\x{6f9}' .
  '\x{966}-\x{96f}\x{9e6}-\x{9ef}\x{9f4}-\x{9f9}\x{a66}-\x{a6f}\x{ae6}-\x{aef}' .
  '\x{b66}-\x{b6f}\x{be7}-\x{bf2}\x{c66}-\x{c6f}\x{ce6}-\x{cef}\x{d66}-\x{d6f}' .
  '\x{e50}-\x{e59}\x{ed0}-\x{ed9}\x{f20}-\x{f33}\x{1040}-\x{1049}\x{1369}-' .
  '\x{137c}\x{16ee}-\x{16f0}\x{17e0}-\x{17e9}\x{17f0}-\x{17f9}\x{1810}-\x{1819}' .
  '\x{1946}-\x{194f}\x{2070}\x{2074}-\x{2079}\x{2080}-\x{2089}\x{2153}-\x{2183}' .
  '\x{2460}-\x{249b}\x{24ea}-\x{24ff}\x{2776}-\x{2793}\x{3007}\x{3021}-\x{3029}' .
  '\x{3038}-\x{303a}\x{3192}-\x{3195}\x{3220}-\x{3229}\x{3251}-\x{325f}\x{3280}-' .
  '\x{3289}\x{32b1}-\x{32bf}\x{ff10}-\x{ff19}');

/**
 * Matches all 'P' Unicode character classes (punctuation)
 */
define('PREG_CLASS_PUNCTUATION',
  '\x{21}-\x{23}\x{25}-\x{2a}\x{2c}-\x{2f}\x{3a}\x{3b}\x{3f}\x{40}\x{5b}-\x{5d}' .
  '\x{5f}\x{7b}\x{7d}\x{a1}\x{ab}\x{b7}\x{bb}\x{bf}\x{37e}\x{387}\x{55a}-\x{55f}' .
  '\x{589}\x{58a}\x{5be}\x{5c0}\x{5c3}\x{5f3}\x{5f4}\x{60c}\x{60d}\x{61b}\x{61f}' .
  '\x{66a}-\x{66d}\x{6d4}\x{700}-\x{70d}\x{964}\x{965}\x{970}\x{df4}\x{e4f}' .
  '\x{e5a}\x{e5b}\x{f04}-\x{f12}\x{f3a}-\x{f3d}\x{f85}\x{104a}-\x{104f}\x{10fb}' .
  '\x{1361}-\x{1368}\x{166d}\x{166e}\x{169b}\x{169c}\x{16eb}-\x{16ed}\x{1735}' .
  '\x{1736}\x{17d4}-\x{17d6}\x{17d8}-\x{17da}\x{1800}-\x{180a}\x{1944}\x{1945}' .
  '\x{2010}-\x{2027}\x{2030}-\x{2043}\x{2045}-\x{2051}\x{2053}\x{2054}\x{2057}' .
  '\x{207d}\x{207e}\x{208d}\x{208e}\x{2329}\x{232a}\x{23b4}-\x{23b6}\x{2768}-' .
  '\x{2775}\x{27e6}-\x{27eb}\x{2983}-\x{2998}\x{29d8}-\x{29db}\x{29fc}\x{29fd}' .
  '\x{3001}-\x{3003}\x{3008}-\x{3011}\x{3014}-\x{301f}\x{3030}\x{303d}\x{30a0}' .
  '\x{30fb}\x{fd3e}\x{fd3f}\x{fe30}-\x{fe52}\x{fe54}-\x{fe61}\x{fe63}\x{fe68}' .
  '\x{fe6a}\x{fe6b}\x{ff01}-\x{ff03}\x{ff05}-\x{ff0a}\x{ff0c}-\x{ff0f}\x{ff1a}' .
  '\x{ff1b}\x{ff1f}\x{ff20}\x{ff3b}-\x{ff3d}\x{ff3f}\x{ff5b}\x{ff5d}\x{ff5f}-' .
  '\x{ff65}');



class Controller_Ajax_Search extends Controller_Ajax_Base_Crud_NoStrict_GET{
    
    protected $_pagination = TRUE;
    
    protected $_table = "Search_Data";
    
    protected $_toFind;


    protected function _get_data() {
        if(!isset($_GET['tofind']) OR (isset($_GET['tofind']) AND $_GET['tofind'] == '') )
            return false;
        
        $this->_toFind = $_GET['tofind']; 
        // escape _ and % for like sql statment
        $this->_toFind = preg_replace('/_/',"\\\\\_" ,$this->_toFind);
        $this->_toFind = preg_replace('/\%/','\\\\\%' ,$this->_toFind);
        
        return DB::select()
                ->from(array('search_datas','s'))
                ->where(DB::expr("s::text"),'ILIKE',"%".$this->_toFind."%")
                ->order_by('order_show_search','DESC');
    }

    protected function _get_list() {
        $ormStart = $this->_get_data();
        
        $orms = $this->_manage_orm_filter_page($ormStart,'execute');

       foreach($orms as $orm)
            $this->_build_res($this->_single_request_row($orm));

       $this->jres->data->items = array_values($this->_res);
    }

    public function _single_request_row($row) {

        return array(
              'type' => $row['type'],
              'id' => $row['id'],
              'title' => $row['title'], 
              'teaser' => $this->_search_excerpt($this->_toFind, $row['description'])
          );

    }

    protected function _search_excerpt($keys, $text)
    {
        $boundary = '(?:(?<=[' . PREG_CLASS_UNICODE_WORD_BOUNDARY . PREG_CLASS_CJK . '])|(?=[' . PREG_CLASS_UNICODE_WORD_BOUNDARY . PREG_CLASS_CJK . ']))';
            
        preg_match_all('/ ("([^"]+)"|(?!OR)([^" ]+))/', ' ' . $keys, $matches);
        $keys = array_merge($matches[2], $matches[3]);

        $text = strip_tags(str_replace(array('<', '>'), array(' <', '> '), $text));
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        array_walk($keys, array($this,'_search_excerpt_replace'));
        $workkeys = $keys;

        $ranges = array();
        $included = array();
        $foundkeys = array();
        $length = 0;

        while ($length < 256 && count($workkeys)) {
            foreach ($workkeys as $k => $key) {
              if (strlen($key) == 0) {
                unset($workkeys[$k]);
                unset($keys[$k]);
                continue;
              }
              if ($length >= 256) {
                break;
              }
              // Remember occurrence of key so we can skip over it if more occurrences
              // are desired.
              if (!isset($included[$key])) {
                $included[$key] = 0;
              }
              // Locate a keyword (position $p, always >0 because $text starts with a
              // space). First try bare keyword, but if that doesn't work, try to find a
              // derived form from search_simplify().
              $p = 0;
              if (preg_match('/' . $boundary . $key . $boundary . '/iu', $text, $match, PREG_OFFSET_CAPTURE, $included[$key])) {
                $p = $match[0][1];
              }
              else {
                $info = $this->_search_simplify_excerpt_match($key, $text, $included[$key], $boundary);
                if ($info['where']) {
                  $p = $info['where'];
                  if ($info['keyword']) {
                    $foundkeys[] = $info['keyword'];
                  }
                }
              }
              // Now locate a space in front (position $q) and behind it (position $s),
              // leaving about 60 characters extra before and after for context.
              // Note that a space was added to the front and end of $text above.
              if ($p) {
                if (($q = strpos(' ' . $text, ' ', max(0, $p - 61))) !== FALSE) {
                  $end = substr($text . ' ', $p, 80);
                  if (($s = strrpos($end, ' ')) !== FALSE) {
                    // Account for the added spaces.
                    $q = max($q - 1, 0);
                    $s = min($s, strlen($end) - 1);
                    $ranges[$q] = $p + $s;
                    $length += $p + $s - $q;
                    $included[$key] = $p + 1;
                  }
                  else {
                    unset($workkeys[$k]);
                  }
                }
                else {
                  unset($workkeys[$k]);
                }
              }
              else {
                unset($workkeys[$k]);
              }
            }
          }
        
        if (count($ranges) == 0) {
            // We didn't find any keyword matches, so just return the first part of the
            // text. We also need to re-encode any HTML special characters that we
            // entity-decoded above.
            return $this->_check_plain(Text::limit_chars($text, 256, '...'));
          }
          else
          {

            // Sort the text ranges by starting position.
            ksort($ranges);

            // Now we collapse overlapping text ranges into one. The sorting makes it O(n).
            $newranges = array();
            foreach ($ranges as $from2 => $to2) {
              if (!isset($from1)) {
                $from1 = $from2;
                $to1 = $to2;
                continue;
              }
              if ($from2 <= $to1) {
                $to1 = max($to1, $to2);
              }
              else {
                $newranges[$from1] = $to1;
                $from1 = $from2;
                $to1 = $to2;
              }
            }
            $newranges[$from1] = $to1;

            // Fetch text
            $out = array();
            foreach ($newranges as $from => $to) {
              $out[] = substr($text, $from, $to - $from);
            }

            // Let translators have the ... separator text as one chunk.
            $dots = explode('!excerpt', '... !excerpt ... !excerpt ...');

            $text = (isset($newranges[0]) ? '' : $dots[0]) . implode($dots[1], $out) . $dots[2];
            $text = $this->_check_plain($text);

            // Slash-escape quotes in keys found in a derived form and merge with original keys.
            array_walk($foundkeys, array($this,'_search_excerpt_replace'));
            $keys = array_merge($keys, $foundkeys);

            // Highlight keywords. Must be done at once to prevent conflicts ('strong' and '<strong>').
            return preg_replace('/' . $boundary . '(' . implode('|', $keys) . ')' . $boundary . '/iu', '<strong>\0</strong>', $text);
          }
    }
    
    protected function _search_simplify_excerpt_match($key, $text, $offset, $boundary) {
        $pos = NULL;
        $simplified_key = $this->_search_simplify($key);
        $simplified_text = $this->_search_simplify($text);

        // Return immediately if simplified key or text are empty.
        if (!$simplified_key || !$simplified_text) {
          return FALSE;
        }

        // Check if we have a match after simplification in the text.
        if (!preg_match('/' . $boundary . $simplified_key . $boundary . '/iu', $simplified_text, $match, PREG_OFFSET_CAPTURE, $offset)) {
          return FALSE;
        }

        // If we get here, we have a match. Now find the exact location of the match
        // and the original text that matched. Start by splitting up the text by all
        // potential starting points of the matching text and iterating through them.
        $split = array_filter(preg_split('/' . $boundary . '/iu', $text, -1, PREG_SPLIT_OFFSET_CAPTURE), '_search_excerpt_match_filter');
        foreach ($split as $value) {
          // Skip starting points before the offset.
          if ($value[1] < $offset) {
            continue;
          }

          // Check a window of 80 characters after the starting point for a match,
          // based on the size of the excerpt window.
          $window = substr($text, $value[1], 80);
          $simplified_window = $this->_search_simplify($window);
          if (strpos($simplified_window, $simplified_key) === 0) {
            // We have a match in this window. Store the position of the match.
            $pos = $value[1];
            // Iterate through the text in the window until we find the full original
            // matching text.
            $length = strlen($window);
            for ($i = 1; $i <= $length; $i++) {
              $keyfound = substr($text, $value[1], $i);
              if ($simplified_key == $this->_search_simplify($keyfound)) {
                break;
              }
            }
            break;
          }
    }

    return $pos ? array('where' => $pos, 'keyword' => $keyfound) : FALSE;
  }
  
  protected function _search_simplify($text)
 {
        // Decode entities to UTF-8
        $text = $this->_decode_entities($text);

        // To improve searching for numerical data such as dates, IP addresses
        // or version numbers, we consider a group of numerical characters
        // separated only by punctuation characters to be one piece.
        // This also means that searching for e.g. '20/03/1984' also returns
        // results with '20-03-1984' in them.
        // Readable regexp: ([number]+)[punctuation]+(?=[number])
        $text = preg_replace('/([' . PREG_CLASS_NUMBERS . ']+)[' . PREG_CLASS_PUNCTUATION . ']+(?=[' . PREG_CLASS_NUMBERS . '])/u', '\1', $text);

        // Multiple dot and dash groups are word boundaries and replaced with space.
        // No need to use the unicode modifer here because 0-127 ASCII characters
        // can't match higher UTF-8 characters as the leftmost bit of those are 1.
        $text = preg_replace('/[.-]{2,}/', ' ', $text);

        // The dot, underscore and dash are simply removed. This allows meaningful
        // search behavior with acronyms and URLs. See unicode note directly above.
        $text = preg_replace('/[._-]+/', '', $text);

        // With the exception of the rules above, we consider all punctuation,
        // marks, spacers, etc, to be a word boundary.
        $text = preg_replace('/[' . PREG_CLASS_UNICODE_WORD_BOUNDARY . ']+/u', ' ', $text);

        // Truncate everything to 50 characters.
        $words = explode(' ', $text);
        array_walk($words, array($this,'_search_index_truncate'));
        $text = implode(' ', $words);

        return $text;
      }

    protected function _search_excerpt_replace(&$text)
    {
        $text = preg_quote($text, '/');
    }
    
    protected function _decode_entities($text) {
        return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
      }
    
    protected function _check_plain($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
     }
     
     protected function _search_index_truncate(&$text) {
        if (is_numeric($text)) {
          $text = ltrim($text, '0');
        }
        $text = Text::limit_chars($text, 50);
      }
     

}