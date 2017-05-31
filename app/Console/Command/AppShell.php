<?php
/**
 * AppShell file
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Shell', 'Console');
App::uses('CakeLog', 'Log');
defined('IS_LOCAL') || define('IS_LOCAL', false);
/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {
	public $connectionDB = null;
	public $db;
    public function mongo_connect() {
		if (is_null($this->connectionDB)) {
			$this->connectionDB = new MongoClient('mongodb://sysadmin:serCurity!2017@localhost:27017?connectTimeoutMS=300000');
			// $this->connectionDB = new Mongo('mongodb://'.IP_CONNECT_MONGODB.':27017?connectTimeoutMS=300000');
			// $this->connectionDB = new Mongo('mongodb://sadmin:2016Anvy!@'.IP_CONNECT_MONGODB.':27017?connectTimeoutMS=300000');
			$this->db = $this->connectionDB->selectDB(DB_CONNECT_MONGODB);
		}
	}

	public function mongo_disconnect() {
		return $this->connectionDB->close();
	}

	public function selectModel($model) {
		$this->mongo_connect();
		if (is_object($this->db) && !is_object($this->$model)) {
			if (file_exists(APP . 'Model' . DS . $model . '.php')) {
				require_once APP . 'Model' . DS . $model . '.php';
				$this->$model = new $model($this->db);
			}
		}
	}

}
