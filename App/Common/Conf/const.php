<?php

defined('TMPL_PATH')    or define('TMPL_PATH',env('TMPL_PATH',null));

defined('IS_JSONP')     or define('IS_JSONP', IS_GET && defined('IS_AJAX') && IS_AJAX && $_SERVER['HTTP_ORIGIN'] );
