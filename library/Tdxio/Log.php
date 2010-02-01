<?php

class Tdxio_Log
{
    const LOGGER_DEST_DB = 'db';
    const LOGGER_DEST_FIREBUG = 'firebug';
	const LOGGER_DEST_FILE = 'file';
	
  	protected static $_loggers;
    protected static $_host;

	private static function init()
    {
		// lazy initialize the logger
		if (!isset(self::$_loggers))
        {
            // configure each type of logger we will support
            $writer = new Zend_Log_Writer_Firebug();
			$fbLogger = new Zend_Log($writer);
            $db = Zend_Db_Table::getDefaultAdapter();
            $colMap = array(
                'message' => 'message',
                'host' => 'host',
                'level' => 'level',
                'created_at' => 'timestamp'
            );
			$config = Zend_Registry::get('config');
			 
            $writer = new Zend_Log_Writer_Db($db, $config->traduxio->log->db->options->table, $colMap);
            $dbLogger = new Zend_Log($writer);

			$writer = new Zend_Log_Writer_Stream($config->traduxio->log->file->options->path);
            $fileLogger = new Zend_Log($writer);
			
			// read from the ini file to see which loggers should be configured for each log level
            foreach ($config->traduxio->log->level as $levelKey => $levelValue)
            {
                foreach (split(',', $levelValue) as $logKey)
                {
                    switch (trim($logKey))
                    {
                        case self::LOGGER_DEST_DB:
                            self::$_loggers[self::convertLevelToInt($levelKey)][$logKey] = &$dbLogger;
                            break;
                        case self::LOGGER_DEST_FIREBUG:
                            self::$_loggers[self::convertLevelToInt($levelKey)][$logKey] = &$fbLogger;
                            break;
						case self::LOGGER_DEST_FILE:
                            self::$_loggers[self::convertLevelToInt($levelKey)][$logKey] = &$fileLogger;
							break;
                        default:
                            throw new Exception('unknown logger type [' . $logKey . ']');
                    }
                }
            }

            // get the current host name so we can include it in the log table
            self::$_host  = (isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:'');
        }
    }
	
	public static function alert($message, $title='')
    {
		self::log($message, $title, Zend_Log::ALERT);
	}

	public static function error($message, $title='')
    {
		self::log($message, $title, Zend_Log::ERR);
	}

	public static function warn($message, $title='')
    {
		self::log($message, $title, Zend_Log::WARN);
	}

	public static function info($message, $title='')
    {
		self::log($message, $title, Zend_Log::INFO);
	}

	public static function debug($message, $title='')
    {
		self::log($message, $title, Zend_Log::DEBUG);
	}

	private static function log($message, $title, $level)
    {
		self::init();
        if (is_null($message)) {
            $message="{NULL}";
        } elseif (is_bool($message)) {
            $message="{".($message ? 'TRUE':'FALSE')."}";
        } elseif (is_array($message) || is_object($message)) {
            $message=print_r($message,true);
        }
        if (null !== $title) $message="[$title] : ".$message;

        // loop through each logger configured for this level and record the log message
        if (is_array(self::$_loggers) &&
           isset(self::$_loggers[$level]) &&
           sizeof(self::$_loggers[$level]) > 0)
        {
            foreach (self::$_loggers[$level] as &$logger)
            {
                $logger->setEventItem('level', self::convertLevelToString($level));
                $logger->setEventItem('host', self::$_host);
                $logger->log($message, $level);
            }
        }
	}	
	
    /*
     * Converts the the string log level into its Zend_Log numeric equivalent
     * @param string corresponding to the log level to convert
     * @return int corresponding to the Zend_Log numerical equivalent
     */
    private static function convertLevelToInt($level)
    {
        $const_name = 'Zend_Log::'. strtoupper($level);
        if (defined($const_name))
        {
            return constant($const_name);
        }
        else
        {
            throw new Exception('invalid log level [' . $level . ']');
        }
    }

    /*
     * Converts the the Zend_Log numeric to its string log level equivalent
     * @param int corresponding to the numeric Zend_Log to convert
     * @return string corresponding to the log level equivalent
     */
    private static function convertLevelToString($level)
    {
        $reflect = new ReflectionClass("Zend_Log");
        $class_constants = $reflect->getConstants();
        foreach ($class_constants as $key => $val)
        {
            if ($val === $level)
            {
                return strtolower($key);
            }
        }

        // if we get this far, we don't have a log level that matches any constants in Zend_Log
        throw new Exception('invalid log level [' . $level . ']');

    }	

} 