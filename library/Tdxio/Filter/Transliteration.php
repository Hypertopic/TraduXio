<?php

require_once 'Zend/Filter/Interface.php';

class Tdxio_Filter_Transliteration implements Zend_Filter_Interface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     * @param  string $value
     * @param  string $srcLang
     * @param  string $destLang
     * @return string
     */
    public function filter ($value)
    {
		/*$langModel = new Model_Language();        
        $browserLang = $langModel->getBrowserLang();			
        Tdxio_Log::info($browserLang,'blang');
		*/		
		$text = $value['text'];
		$srcLang = $value['srcLang'];
		$destLang = $value['destLang'];
		$supportedLangs = array('ces','dan','deu','fra','hrv','hun','pol','rus');
		if(!in_array($srcLang,$supportedLangs)){return $text;}
		switch($srcLang){
			case 'ces': $table = $this->_transliterateCs(); break;
			case 'dan': $table = $this->_transliterateDa(); break;
			case 'deu': $table = $this->_transliterateDe(); break;
			case 'fra': $table = $this->_transliterateFr(); break;
			case 'hrv': $table = $this->_transliterateHr(); break;
			case 'hun': $table = $this->_transliterateHu(); break;
			case 'pol': $table = $this->_transliteratePl(); break;
			case 'rus': $table = $this->_transliterateRu(); break;
			default: $table=null;break;						
		}
		if(!is_null($table)){
			Tdxio_Log::info($table,'tabella traslitterazione');
			/*foreach($table as $key=>$val)
				if($val == '')
					unset($table[$key]);
			$text = strtr($text,array_flip($table));*/
			$text = strtr($text,$table);
			return $text;
		}else{
			return $text;
		} 
    }

    /**
     * Transliterate Russian chars (Cyrillic)
     *
     * @param string $s
     * @return string
     */
    private function _transliterateRu()
    {
        $table = array (
            "А" => "A",
            "Б" => "B",
            "В" => "V",
            "Г" => "G",
            "Д" => "D",
            "Є" => "E",
            "Е" => "JE",
            "Ё" => "JO",
            "Ж" => "ZH",
            "З" => "Z",
            "И" => "I",
            "Й" => "J",
            "К" => "K",
            "Л" => "L",
            "М" => "M",
            "Н" => "N",
            "О" => "O",
            "П" => "P",
            "Р" => "R",
            "С" => "S",
            "Т" => "T",
            "У" => "U",
            "Ф" => "F",
            "Х" => "KH",
            "Ц" => "TS",
            "Ч" => "CH",
            "Ш" => "SH",
            "Щ" => "SHCH",
            "Ъ" => "",
            "Ы" => "Y",
            "Ь" => "",
            "Э" => "E",
            "Ю" => "JU",
            "Я" => "JA",
            "Ґ" => "G",
            "Ї" => "I",
            "а" => "a",
            "б" => "b",
            "в" => "v",
            "г" => "g",
            "д" => "d",
            "є" => "e",
            "е" => "je",
            "ё" => "jo",
            "ж" => "zh",
            "з" => "z",
            "и" => "i",
            "й" => "j",
            "к" => "k",
            "л" => "l",
            "м" => "m",
            "н" => "n",
            "о" => "o",
            "п" => "p",
            "р" => "r",
            "с" => "s",
            "т" => "t",
            "у" => "u",
            "ф" => "f",
            "х" => "kh",
            "ц" => "ts",
            "ч" => "ch",
            "ш" => "sh",
            "щ" => "shch",
            "ъ" => "",
            "ы" => "y",
            "ь" => "",
            "э" => "e",
            "ю" => "ju",
            "я" => "ja",
            "ґ" => "g",
            "ї" => "i"
        );
        return $table;
    }
    
        /**
     * Transliterate Czech chars
     *
     * @param string $s
     * @return string
     */
    private function _transliterateCs ()
    {
        $table = array (
            'á' => 'a',
            'č' => 'c',
            'ď' => 'd',
            'é' => 'e',
            'ě' => 'e',
            'í' => 'i',
            'ň' => 'n',
            'ó' => 'o',
            'ř' => 'r',
            'š' => 's',
            'ť' => 't',
            'ú' => 'u',
            'ů' => 'u',
            'ý' => 'y',
            'ž' => 'z',
            'Á' => 'A',
            'Č' => 'C',
            'Ď' => 'D',
            'É' => 'E',
            'Ě' => 'E',
            'Í' => 'I',
            'Ň' => 'N',
            'Ó' => 'O',
            'Ř' => 'R',
            'Š' => 'S',
            'Ť' => 'T',
            'Ú' => 'U',
            'Ů' => 'U',
            'Ý' => 'Y',
            'Ž' => 'Z',
        );
        return $table;
    }
    
        /**
     * Transliterate German chars
     *
     * @param string $s
     * @return string
     */
    private function _transliterateDe ()
    {
        $table = array (
            'ä' => 'ae',
            'ë' => 'e',
            'ï' => 'i',
            'ö' => 'oe',
            'ü' => 'ue',
            'Ä' => 'Ae',
            'Ë' => 'E',
            'Ï' => 'I',
            'Ö' => 'Oe',
            'Ü' => 'Ue',
            'ß' => 'ss',
        );
        return $table;
    }
    
        /**
     * Transliterate French chars
     *
     * @param string $s
     * @return string
     */
    private function _transliterateFr ()
    {        
        $table = array (
            'â' => 'a',
            'ê' => 'e',
            'î' => 'i',
            'ô' => 'o',
            'û' => 'u',
            'Â' => 'A',
            'Ê' => 'E',
            'Î' => 'I',
            'Ô' => 'O',
            'Û' => 'U',
            'œ' => 'oe',
            'æ' => 'ae',
            'Ÿ' => 'Y',
            'ç' => 'c',
			'Ç' => 'C',
        );
        return $table;
    }
    
        /**
     * Transliterate Hungarian chars
     *
     * @param string $s
     * @return string
     */
    private function _transliterateHu ()
    {        
        $table = array (
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ö' => 'o',
            'ő' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ű' => 'u',
        );
        return $table;
    }

    /**
     * Transliterate Polish chars
     *
     * @param string $s
     * @return string
     */
    private function _transliteratePl ()
    {
        $table = array(
        'ą' => 'a', 
        'ę' => 'e', 
        'ó' => 'o', 
        'ć' => 'c', 
        'ł' => 'l', 
        'ń' => 'n', 
        'ś' => 's', 
        'ż' => 'z', 
        'ź' => 'z', 
        'Ó' => 'O', 
        'Ć' => 'C', 
        'Ł' => 'L', 
        'Ś' => 'S', 
        'Ż' => 'Z', 
        'Ź' => 'Z' 
        );
        return $table;
    }

        /**
     * Transliterate Danish chars
     *
     * @param string $s
     * @return string
     */
    private function _transliterateDa ()
    {
        $table = array(
        'æ' => 'ae', 
        'ø' => 'oe', 
        'å' => 'aa', 
        'Æ' => 'Ae', 
        'Ø' => 'Oe', 
        'Å' => 'Aa' 
        );
        return $table;
    }
    
        /**
     * Transliterate Croatian chars
     *
     * @param string $s
     * @return string
     */ 
    private function _transliterateHr () 
    { 
        $table = array ( 
            'Č' => 'C', 
            'Ć' => 'C', 
            'Ž' => 'Z', 
            'Š' => 'S', 
            'Đ' => 'D', 
            'č' => 'c', 
            'ć' => 'c', 
            'ž' => 'z', 
            'š' => 's', 
            'đ' => 'd', 
        );
        return $table; 
    }

}
