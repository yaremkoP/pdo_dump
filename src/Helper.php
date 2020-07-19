<?php


namespace App;


class Helper
{
    /**
     * Check if filepath contains `\|` characters
     * @param string $filepath
     *
     * @return bool
     */
    public static function isIllegalPath(string $filepath): bool
    {
        return preg_match('~([\\\/]+)~',$filepath);
    }

    /**
     * Return string doesn't contain `\|` characters
     *
     * @param string $file_path
     *
     * @return string
     */
    public static function trimFileName(string $file_path): string
    {
        $parts = explode('/', $file_path);
        $file = array_pop($parts);
        if ($file === $file_path) {
            $parts = explode('\\', $file_path);
            $file = array_pop($parts);
        }

        return $file;
    }

    /**
     * Create folder relevant from Base dir
     *
     * @param string $folder_name
     */
    public static function createTmpFolder(string $folder_name)
    {
        if (!is_dir($dirname = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . $folder_name)) {
            mkdir($dirname, 0777);
        } else {
            chmod($dirname, 0777);
        }
    }

}