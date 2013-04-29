<?php
/**
 * Generic static logging class with 3 levels of logging.
 * - The file and line number of where the log was thrown are automatically added to the log item
 * - Optionally you may pass a "context", which can be any string and is supposed to give a clue to where the sollution can be found
 * 
 * Log it:
 * Log::info($msg)
 * Log::warn($msg[, $context])
 * Log::error($msg[, $context])
 * 
 * Do we have any logs? Request can be filtered by type/level:
 * Log::has(['info'|'warn'|'error'])
 * 
 * Get a raw array or output directly to screen. Can be filtered by type/level:
 * Log::retrieve()
 * Log::output()
 * 
 * Clearing the log (can also be filtered):
 * Log::clear()
 */
class Log {
	private static $logs = array();

	/**
	 * Normal log
	 * @param type $msg
	 * @param string [optional] $ctx Additional context info
	 */
	public static function info($msg, $ctx = '') {
		self::addLog('info', $msg, $ctx);
	}
	
	/**
	 * Log a warning
	 * @param string $msg
	 * @param string [optional] $ctx Context where user can fix this issue
	 */
	public static function warn($msg, $ctx = '') {
		self::addLog('warn', $msg, $ctx);
	}
	
	/**
	 * Log an error
	 * @param string $msg
	 * @param string [optional] $ctx Context where user can fix this issue
	 */
	public static function error($msg, $ctx = '') {
		self::addLog('error', $msg, $ctx);
	}
	
	/**
	 * Severe logs get more information so they can be debugged.
	 * @param string $severity
	 * @param string $msg
	 * @param string $ctx
	 */
	private static function addLog($severity, $msg, $ctx) {
		$debug = debug_backtrace(); // Gives info from where the log is thrown
		$i = count(self::$logs);
		
		self::$logs[$i] = array(
			'severity' => $severity,
			'message' => $msg,
			'in_file' => $debug[1]['file'], // [1] displays info caller of warn() and/or error()
			'at_line' => $debug[1]['line']
		);
		
		// Additionally let the user know where it can be fixed (optionally passed
		// when the cause of the error is somewhere else from where it's thrown)
		if ($ctx) {
			self::$logs[$i]['context'] = $ctx;
		}
	}

	/**
	 * Check if a log is made for given type (or any)
	 * @param string $type
	 * @return boolean
	 */
	public static function has($type = '') {
		if ($type === '' && count(self::$logs) > 0) {
			return true;
		}
		
		foreach (self::$logs as $log) {
			if ($log['severity'] === $type) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Retrieve raw array of log for given type (or any)
	 * @param string $type
	 * @return array
	 */
	public static function retrieve($type = '') {
		if ($type) {
			$typelogs = array();
			foreach (self::$logs as $log) {
				if ($log['severity'] === $type) {
					$typelogs[] = $log;
				}
			}
			return $typelogs;
		} else {
			return self::$logs;
		}
	}
	
	/**
	 * Output log for given type (or any) to screen
	 * @TODO Make this look nice
	 * @param string $type
	 */
	public static function output($type = '') {
		echo '<pre>';
		print_r(self::retrieve($type));
		echo '</pre>';
	}
	
	/**
	 * Clear requested logs. If type filter is given, the relative order of
	 * logs is preserved
	 * @param string $type
	 */
	public static function clear($type = '') {
		if ($type) {
			$l = count(self::$logs);
			for ($i = 0; $i < $l; $i++) {
				if (self::$logs[$i]['severity'] === $type) {
					unset(self::$logs[$i]);
				}
			}
			self::$logs = array_values(self::$logs);
		} else {
			self::$logs = array();
		}
	}
}