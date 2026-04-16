<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| API Configuration
|--------------------------------------------------------------------------
|
| Configuration for external API integration.
|
| Usage:
|   $this->config->item('api_url')
|   $this->config->item('api_username')
|   $this->config->item('api_password')
|
*/

// API endpoint and authentication credentials
// These can be set via server environment variables
$config['api_url'] = isset($_SERVER['API_URL']) ? $_SERVER['API_URL'] : '';
$config['api_username'] = isset($_SERVER['API_USER']) ? $_SERVER['API_USER'] : '';
$config['api_password'] = isset($_SERVER['API_PASSWORD']) ? $_SERVER['API_PASSWORD'] : '';
