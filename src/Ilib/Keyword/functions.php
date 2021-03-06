<?php   
// Dynamic global functions
if (!function_exists('safeToDb')) {
    /**
     * This function is dynamically redefinable.
     * @see $GLOBALS['_global_function_callback_e']
     */
    function safeToDb($args) 
    {
        $args = func_get_args();
        return call_user_func_array($GLOBALS['_global_function_callback_safetodb'], $args);
    }
    if (!isset($GLOBALS['_global_function_callback_safetodb'])) {
        $GLOBALS['_global_function_callback_safetodb'] = NULL;
    }
}
$GLOBALS['_global_function_callback_safetodb'] = 'ilib_keyword_safetodb';

/**
 * Function to be called before putting data in the database
 *
 * @author  Lars Olesen <lars@legestue.net>
 */
function ilib_keyword_safetodb($data) 
{
    if (is_object($data)) {
        return $data;
    }
    
    if(is_array($data)){
        return array_map('ilib_keyword_safetodb',$data);
    }

    if (get_magic_quotes_gpc()) {
        $data = stripslashes($data);
    }

    return mysql_escape_string(trim($data));
}