<?php

namespace ConnectedTraffic\Component;

// configuration...
class Config{
	const SERVER_NAME = 'ConnectedTraffic';
	const VERSION = '0.1';
	const BIND_ADDRESS = '0.0.0.0';
	const BIND_PORT = '9876';
	const PING_TIMEOUT = 10;

	const LOCATION_USER_DATA = 'data/user/';
	const LOCATION_CONVERSATION_DATA = 'data/conversation/';

	const DATE_FORMAT = 'd-m-Y H:i:s';

	const LOG_TYPE = 'console,file';
	const LOG_FILE = 'log.txt';
	const LOG_ERRORS = true;
	const LOG_WARNINGS = true;
	const LOG_INFOS = true;
	const LOG_DEBUGS = true;

	const CONTROLLER_DIR = 'protected/controller/';

}
?>
