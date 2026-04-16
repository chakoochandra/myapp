<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'third_party/elfinder/php/autoload.php';

class Elfinder_lib
{
  public function __construct($opts = array())
  {
    elFinder::$netDrivers['ftp'] = 'FTP';

    $defaults = $this->get_defaults();

    // If "roots" is provided, merge each root individually with the corresponding default
    if (isset($opts['roots'])) {
      foreach ($opts['roots'] as $index => $rootOverride) {
        if (isset($defaults['roots'][$index])) {
          $defaults['roots'][$index] = array_replace_recursive($defaults['roots'][$index], $rootOverride);
        } else {
          // Append extra roots if more are passed than defaults
          $defaults['roots'][] = $rootOverride;
        }
      }
      unset($opts['roots']); // remove to avoid overwriting below
    }

    // Merge remaining config keys deeply
    $opts = array_replace_recursive($defaults, $opts);

    $connector = new elFinderConnector(new elFinder($opts));
    $connector->run();
  }

  protected function get_defaults()
  {
    $CI = &get_instance();
    $CI->load->helper('path');

    return array(
      'roots' => array(
        array(
          'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
          'path'          => './uploads',
          'URL'           => site_url('uploads') . '/',
          'trashHash'     => 't1_Lw',                     // elFinder's hash of trash folder
          'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
          'uploadDeny'    => array('all'),                // All Mimetypes not allowed to upload
          'uploadAllow'   => array('image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/x-icon', 'text/plain', 'application/msword', 'application/rtf', 'text/rtf'), // Mimetype `image` and `text/plain` allowed to upload
          'uploadOrder'   => array('deny', 'allow'),      // allowed Mimetype `image` and `text/plain` only
          'accessControl' => array($this, 'elfinder_access')                // disable and hide dot starting files (OPTIONAL)
        ),
        // Trash volume
        array(
          'id'            => '1',
          'driver'        => 'Trash',
          'path'          => './uploads/.trash',
          'tmbURL'        => site_url('uploads/.tmb/'),
          'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
          'uploadDeny'    => array('all'),                // Recomend the same settings as the original volume that uses the trash
          'uploadAllow'   => array('image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/x-icon', 'text/plain', 'application/msword', 'application/rtf', 'text/rtf'), // Same as above
          'uploadOrder'   => array('deny', 'allow'),      // Same as above
          'accessControl' => array($this, 'elfinder_access'),                    // Same as above
        ),
      ),
      'plugin' => [
        'WinRemoveTailDots' => [
          'enable' => true
        ]
      ]
    );
  }

  /**
   * Simple function to demonstrate how to control file access using "accessControl" callback.
   * This method will disable accessing files/folders starting from '.' (dot)
   *
   * @param  string    $attr    attribute name (read|write|locked|hidden)
   * @param  string    $path    absolute file path
   * @param  string    $data    value of volume option `accessControlData`
   * @param  object    $volume  elFinder volume driver object
   * @param  bool|null $isDir   path is directory (true: directory, false: file, null: unknown)
   * @param  string    $relpath file path relative to volume root directory started with directory separator
   * @return bool|null
   **/
  public function elfinder_access($attr, $path, $data, $volume, $isDir, $relpath)
  {
    $basename = basename($path);
    return $basename[0] === '.'                  // if file/folder begins with '.' (dot)
      && strlen($relpath) !== 1           // but with out volume root
      ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
      :  null;
  }
}
