<?php
if (!defined('ABSPATH')) { exit; }
/** Compatibility wrapper; editable content seeds live in the business plugin. */
function fp_theme_service_seed() { return function_exists('fp_service_seed') ? fp_service_seed() : array(); }
