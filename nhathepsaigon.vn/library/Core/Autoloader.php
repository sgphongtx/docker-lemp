<?php

/**
 * Description of Loader
 *
 * @author PhongTX
 */
class Core_Autoloader
{

    public function loadClass($className)
    {
        if (class_exists($className, false) || interface_exists($className, false))
        {
            return;
        }
        $classFile = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        if (function_exists('stream_resolve_include_path') !== false)
        {
            $file = stream_resolve_include_path($classFile);
        }
        else
        {
            $dir = explode(PATH_SEPARATOR, get_include_path());
            foreach ($dir as $path)
            {
                if (is_readable($path . '/' . $classFile))
                {
                    $file = $path . '/' . $classFile;
                    break;
                }
            }
        }
        if ($file != false)
        {
            include_once ($file);
        }
        return true;
    }

}

?>
