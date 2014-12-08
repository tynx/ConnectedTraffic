<?php

// Base
require_once('ConnectedTrafficServer.class.php');

//Component
//require_once('Component/Config.class.php');

// Component (Logging)
require_once('Component/Logging/Logger.class.php');
require_once('Component/Logging/LogRoute.class.php');
require_once('Component/Logging/FileLogRoute.class.php');
require_once('Component/Logging/ConsoleLogRoute.class.php');

// Controller
require_once('Controller/ConnectionController.class.php');
require_once('Controller/RequestController.class.php');

// Model
require_once('Model/Connection.class.php');
require_once('Model/Client.class.php');

// Model (Frame)
require_once('Model/Frame/Frame.class.php');
require_once('Model/Frame/InboundFrame.class.php');
require_once('Model/Frame/OutboundFrame.class.php');

// Model (Request)
require_once('Model/Request/Request.class.php');
require_once('Model/Request/RequestHeader.class.php');
require_once('Model/Request/ParserInterface.php');
require_once('Model/Request/JSONParser.class.php');

// Model (Response)
require_once('Model/Response/Response.class.php');
require_once('Model/Response/ResponseHeader.class.php');
require_once('Model/Response/SerializerInterface.php');
require_once('Model/Response/JSONSerializer.class.php');

// Helper
require_once('Helper/Handshake.class.php');
require_once('Helper/Masking.class.php');

// Exceptions
require_once('Exception/InvalidConfigException.class.php');

// Public for interaction
require_once('Public/Message.class.php');
require_once('Public/BaseRequestController.class.php');
require_once('Public/BaseEventController.class.php');