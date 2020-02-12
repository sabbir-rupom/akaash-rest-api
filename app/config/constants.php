<?php

/**
 * Write all your application constants in here
 */

// Set numeric value for client end platforms
const PLATFORM_TYPE_WEB = 0;
const PLATFORM_TYPE_IOS = 1;
const PLATFORM_TYPE_ANDROID = 2;

/**
 * Maintenance Enable
 * @var int
 */
const MAINTENANCE_ON = 1;

/*
 * JWT Token verification error codes
 */
const HASH_SIGNATURE_VERIFICATION_FAILED = 1;
const EMPTY_TOKEN = 5;

/**
 * File upload path(s)
 */
const UPLOAD_PATH = ROOT_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
