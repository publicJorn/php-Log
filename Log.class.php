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
 * Log::count(['info'|'warn'|'error'])
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
	 * @param mixed [optional] $ctx Additional context info (see addLog for details)
	 */
	public static function info($msg, $ctx = '') {
		self::addLog('info', $msg, $ctx);
	}
	
	/**
	 * Log a warning
	 * @param string $msg
	 * @param mixed [optional] $ctx Context where user can fix this issue (see addLog for details)
	 */
	public static function warn($msg, $ctx = '') {
		self::addLog('warn', $msg, $ctx);
	}
	
	/**
	 * Log an error
	 * @param string $msg
	 * @param mixed [optional] $ctx Context where user can fix this issue (see addLog for details)
	 */
	public static function error($msg, $ctx = '') {
		self::addLog('error', $msg, $ctx);
	}
	
	/**
	 * Severe logs get more information so they can be debugged.
	 * @param string $severity
	 * @param string $msg
	 * @param mixed $ctx Context of log item. If a number, the debug_backtrace will go 
	 *                   further back in history. If a string, it will be added as such
	 *                   to the log array
	 */
	private static function addLog($severity, $msg, $ctx) {
		$debug = debug_backtrace(); // Gives info from where the log is thrown
		$history = is_int($ctx)? 1 + $ctx : 1; // 1 displays info caller of warn() and/or error()
		$i = count(self::$logs);
		
		self::$logs[$i] = array(
			'severity' => $severity,
			'message' => $msg,
			'in_file' => $debug[$history]['file'],
			'at_line' => $debug[$history]['line']
		);
		
		// Set context if not specified -uses classname if available, defaults to filename
		if ((!$ctx || is_int($ctx)) && isset($debug[0]['class'])) {
			$ctx = $debug[0]['class'];
		}
		
		// If context is a string, we'll append it to the log
		if ($ctx && is_string($ctx)) {
			self::$logs[$i]['context'] = $ctx;
		}
	}

	/**
	 * Check if a log is made for given type (or any)
	 * @param string $type
	 * @return boolean
	 */
	public static function count($type = '') {
		$total = count(self::$logs);
		if ($type === '' && $total > 0) {
			return $total;
		}

		$i = 0;
		foreach (self::$logs as $log) {
			if ($log['severity'] === $type) {
				$i++;
			}
		}
		return $i;
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
