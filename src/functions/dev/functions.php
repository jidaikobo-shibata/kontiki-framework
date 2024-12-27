<?php

if (!function_exists('jlog')) {
    /**
     * Log function for development
     *
     * @param mixed $messages message
     * @return void
     */
    function jlog($messages)
    {
        \jidaikobo\Log::write($messages);
    }
}
