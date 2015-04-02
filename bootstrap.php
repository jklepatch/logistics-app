<?php
/**
 * @since 1.0
 *
 * Bootstrap file
 *
 * Loads:
 *   - config.php where important constant are defined (db credentials, file system, etc..)
 *   - all files in /app/inc directory (utility functions, interactions with ftp files functions, etc...)
 *   - render.php to for views
 *   - the ORM (class-scorpio-orm.php) and all models (shipments, remarks and stock)
 */

//Setup constants
require_once 'config.php';

//Call dependencies
require_once LOCAL_INC_DIR . '\utils.php';
require_once LOCAL_INC_DIR . '\request.php';
require_once LOCAL_INC_DIR . '\ftp.php';
require_once LOCAL_INC_DIR . '\render.php';
require_once LOCAL_MODELS_DIR . '\class-scorpio-orm.php';
require_once LOCAL_MODELS_DIR . '\class-base-model.php';
require_once LOCAL_MODELS_DIR . '\class-shipments.php';
require_once LOCAL_MODELS_DIR . '\class-remarks.php';
require_once LOCAL_MODELS_DIR . '\class-stock.php';

