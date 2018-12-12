<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Client edition constant class
 *
 * It is intended to distinguish the DAU for each edition at the time of KPI analysis.
 */
class Const_ClientEdition {

    /**
     * Client Edition : unknown
     */
    const UNKNOWN = 0;

    /**
     * Client Edition : iOS (App Store Version)
     */
    const IOS_PLATFORM = 1;

    /**
     * Client Edition : Android Domestic (Google Play edition)
     */
    const ANDROID_PLATFORM_GOOGLE_PLAY = 2;

}
