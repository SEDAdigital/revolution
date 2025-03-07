<?php
/*
 * This file is part of MODX Revolution.
 *
 * Copyright (c) MODX, LLC. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */
/*
 * This file is part of MODX Revolution.
 *
 * Copyright (c) MODX, LLC. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */
abstract class modConfigReader {
    /** @var modInstall $install */
    public $install;
    /** @var xPDO $xpdo */
    public $xpdo;
    /** @var array $config */
    public $config = [];

    function __construct(modInstall $install,array $config = []) {
        $this->install =& $install;
        $this->xpdo =& $install->xpdo;
        $this->config = array_merge([

        ],$config);
    }

    /**
     * Read an existing configuration file
     * @abstract
     * @param array $config
     */
    abstract public function read(array $config = []);

    /**
     * Load defaults for a configuration file if one does not exist; used in new installations
     * @param array $config
     * @return array
     */
    public function loadDefaults(array $config = []) {
        $this->getHttpHost();

        $this->config = array_merge($this->config, [
            'database_type' => isset ($_POST['databasetype']) ? $_POST['databasetype'] : 'mysql',
            'database_server' => isset ($_POST['databasehost']) ? $_POST['databasehost'] : 'localhost',
            'database_connection_charset' => 'utf8',
            'database_charset' => 'utf8',
            'dbase' => trim((isset ($_POST['database_name']) ? $_POST['database_name'] : 'modx'), '`[]'),
            'database_user' => isset ($_POST['databaseloginname']) ? $_POST['databaseloginname'] : '',
            'database_password' => isset ($_POST['databaseloginpassword']) ? $_POST['databaseloginpassword'] : '',
            'table_prefix' => isset ($_POST['tableprefix']) ? $_POST['tableprefix'] : 'modx_',
            'site_sessionname' => 'SN' . uniqid(''),
            'inplace' => isset ($_POST['inplace']) ? 1 : 0,
            'unpacked' => isset ($_POST['unpacked']) ? 1 : 0,
            'config_options' => [],
            'driver_options' => [],
        ],$config);
        return $this->config;
    }

    /**
     * Get the HTTP host for the server
     */
    public function getHttpHost() {
        if (php_sapi_name() != 'cli') {
            $this->config['https_port'] = isset ($_POST['httpsport']) ? $_POST['httpsport'] : '443';
            $isSecureRequest = ((isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') || $_SERVER['SERVER_PORT'] == $this->config['https_port']);
            $this->config['http_host']= $_SERVER['HTTP_HOST'];
            if ($_SERVER['SERVER_PORT'] != 80) {
                $this->config['http_host']= str_replace(':' . $_SERVER['SERVER_PORT'], '', $this->config['http_host']); /* remove port from HTTP_HOST */
            }
            $this->config['http_host'] .= in_array($_SERVER['SERVER_PORT'], [80, 443]) ? '' : ':' . $_SERVER['SERVER_PORT'];
        } else {
            $this->config['http_host'] = 'localhost';
            $this->config['https_port'] = 443;
        }
    }
}
