php-Log
=======
Generic static logging class with 3 levels of logging.
- The file and line number of where the log was thrown are automatically added to the log item if it is severe (warn or error)
- Optionally you may pass a "context", which can be any string and is supposed to give a clue to where the sollution can be found

Log it:
Log::info($msg)
Log::warn($msg[, $context])
Log::error($msg[, $context])

Do we have any logs? Request can be filtered by type/level:
Log::has(['info'|'warn'|'error'])

Get a raw array or output directly to screen. Can be filtered by type/level:
Log::retrieve()
Log::output()

Clearing the log (can also be filtered):
Log::clear()
