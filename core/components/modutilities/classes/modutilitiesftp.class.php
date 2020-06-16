<?php

	class modUtilitiesFtp
	{
		/* @var modX $modX */
		public $modx;
		/* @var modUtilities $util */
		public $util;

		/**
		 * FTP host
		 *
		 * @var string $_host
		 */
		private $_host;

		/**
		 * FTP port
		 *
		 * @var int $_port
		 */
		private $_port = 21;

		/**
		 * FTP password
		 *
		 * @var string $_pwd
		 */
		private $_pwd;

		/**
		 * FTP stream
		 *
		 * @var resource $_stream
		 */
		private $_stream;

		/**
		 * FTP timeout
		 *
		 * @var int $_timeout
		 */
		private $_timeout = 90;

		/**
		 * FTP user
		 *
		 * @var string $_user
		 */
		private $_user;

		/**
		 * Last error
		 *
		 * @var array $error
		 */
		public $error = [];

		/**
		 * FTP passive mode flag
		 *
		 * @var bool $passive
		 */
		public $passive = FALSE;

		/**
		 * SSL-FTP connection flag
		 *
		 * @var bool $ssl
		 */
		public $ssl = FALSE;

		/**
		 * System type of FTP server
		 *
		 * @var string $system_type
		 */
		public $system_type;
		public $dir;
		public $flileList = [];


		/**
		 * Initialize connection params
		 *
		 * @param string $host
		 * @param string $user
		 * @param string $password
		 * @param int    $port
		 * @param int    $timeout (seconds)
		 */
		public function __construct(modX $modx, modUtilities $util, $param)
		{
			$this->modx = $modx;
			$this->util = $util;
			$this->_host = (string)$param['host'] ?: NULL;
			$this->_user = (string)$param['user'] ?: NULL;
			$this->_pwd = (string)$param['password'] ?: NULL;
			$this->ssl = (bool)$param['ssl'] ?: FALSE;
			$this->_port = (int)$param['port'] ?: 21;
			$this->_timeout = (int)$param['timeout'] ?: 90;
			$this->connect();
			if($this->isConnected()){
				$this->pwd();
				$this->ls();

			}
		}

		/**
		 * @return mixed
		 */
		public function __toString()
		{
			return $this->isConnected() ? '1' : '0';
		}

		/**
		 * Auto close connection
		 */
		public function __destruct()
		{
			$this->close();
		}

		public function isConnected()
		{
			return (is_resource($this->_stream)) ? TRUE : FALSE;
		}

		/**
		 * Change currect directory on FTP server
		 *
		 * @param string $directory
		 * @return bool
		 */
		public function cd($directory = NULL)
		{
			// attempt to change directory
			if (ftp_chdir($this->_stream, $directory)) {
				// success
				$this->dir = $this->pwd();
				return TRUE;
				// fail
			} else {
				$this->error[] = "Failed to change directory to \"{$directory}\"";
				return FALSE;
			}
		}

		public function isDir($directory){
			// attempt to change directory
			if (ftp_chdir($this->_stream, $directory)) {
				// success
				ftp_chdir($this->_stream, $this->dir);
				return TRUE;
			} else {
				// fail
				return FALSE;
			}
		}

		/**
		 * Set file permissions
		 *
		 * @param int    $permissions (ex: 0644)
		 * @param string $remote_file
		 * @return false
		 */
		public function chmod($permissions = 0, $remote_file = NULL)
		{
			// attempt chmod
			if (ftp_chmod($this->_stream, $permissions, $remote_file)) {
				// success
				return TRUE;
				// failed
			} else {
				$this->error[] = "Failed to set file permissions for \"{$remote_file}\"";
				return FALSE;
			}
		}

		/**
		 * Connect to FTP server
		 *
		 * @return bool
		 */
		public function connect()
		{
			// check if non-SSL connection
			if (!$this->ssl) {
				// attempt connection
				if (!$this->_stream = ftp_connect($this->_host, $this->_port, $this->_timeout)) {
					// set last error
					$this->error[] = "Failed to connect to {$this->_host}";
					return FALSE;
				}
				// SSL connection
			} elseif (function_exists("ftp_ssl_connect")) {
				// attempt SSL connection
				if (!$this->_stream = ftp_ssl_connect($this->_host, $this->_port, $this->_timeout)) {
					// set last error
					$this->error[] = "Failed to connect to {$this->_host} (SSL connection)";
					return FALSE;
				}
				// invalid connection type
			} else {
				$this->error[] = "Failed to connect to {$this->_host} (invalid connection type)";
				return FALSE;
			}

			// attempt login
			if (ftp_login($this->_stream, $this->_user, $this->_pwd)) {
				// set passive mode
				ftp_pasv($this->_stream, (bool)$this->passive);

				// set system type
				$this->system_type = ftp_systype($this->_stream);

				// connection successful
				return TRUE;
				// login failed
			} else {
				$this->error[] = "Failed to connect to {$this->_host} (login failed)";
				return FALSE;
			}
		}

		/**
		 * Delete file on FTP server
		 *
		 * @param string $remote_file
		 * @return bool
		 */
		public function delete($remote_file = NULL)
		{
			// attempt to delete file
			if (ftp_delete($this->_stream, $remote_file)) {
				// success
				return TRUE;
				// fail
			} else {
				$this->error[] = "Failed to delete file \"{$remote_file}\"";
				return FALSE;
			}
		}

		/**
		 * Download file from server
		 *
		 * @param string $remote_file
		 * @param string $local_file
		 * @param int    $mode
		 * @return bool
		 */
		public function get($remote_file = NULL, $local_file = NULL, $mode = FTP_ASCII)
		{
			// attempt download
			if (ftp_get($this->_stream, $local_file, $remote_file, $mode)) {
				// success
				return TRUE;
				// download failed
			} else {
				$this->error[] = "Failed to download file \"{$remote_file}\"";
				return FALSE;
			}
		}

		/**
		 * Get list of files/directories in directory
		 *
		 * @param string $directory
		 * @return array
		 */
		public function ls($directory = NULL)
		{
			$list = [];

			// attempt to get list
			if ($list = ftp_nlist($this->_stream, $directory)) {
				// success
				$this->flileList = $list;
				return $list;
				// fail
			} else {
				$this->error[] = "Failed to get directory list";
				return [];
			}
		}

		/**
		 * Create directory on FTP server
		 *
		 * @param string $directory
		 * @return bool
		 */
		public function mkdir($directory = NULL)
		{
			// attempt to create dir
			if (ftp_mkdir($this->_stream, $directory)) {
				// success
				return TRUE;
				// fail
			} else {
				$this->error[] = "Failed to create directory \"{$directory}\"";
				return FALSE;
			}
		}

		/**
		 * Upload file to server
		 *
		 * @param string $local_path
		 * @param string $remote_file_path
		 * @param int    $mode
		 * @return bool
		 */
		public function put($local_file = NULL, $remote_file = NULL, $mode = FTP_ASCII)
		{
			// attempt to upload file
			if (ftp_put($this->_stream, $remote_file, $local_file, $mode)) {
				// success
				return TRUE;
				// upload failed
			} else {
				$this->error[] = "Failed to upload file \"{$local_file}\"";
				return FALSE;
			}
		}

		/**
		 * Get current directory
		 *
		 * @return string
		 */
		public function pwd()
		{
			return $this->dir = ftp_pwd($this->_stream);
		}

		/**
		 * Rename file on FTP server
		 *
		 * @param string $old_name
		 * @param string $new_name
		 * @return bool
		 */
		public function rename($old_name = NULL, $new_name = NULL)
		{
			// attempt rename
			if (ftp_rename($this->_stream, $old_name, $new_name)) {
				// success
				return TRUE;
				// fail
			} else {
				$this->error[] = "Failed to rename file \"{$old_name}\"";
				return FALSE;
			}
		}

		/**
		 * Remove directory on FTP server
		 *
		 * @param string $directory
		 * @return bool
		 */
		public function rmdir($directory = NULL)
		{
			// attempt remove dir
			if (ftp_rmdir($this->_stream, $directory)) {
				// success
				return TRUE;
				// fail
			} else {
				$this->error[] = "Failed to remove directory \"{$directory}\"";
				return FALSE;
			}
		}

		/**
		 * Close FTP connection
		 */
		public function close()
		{
			// check for valid FTP stream
			if ($this->_stream) {
				// close FTP connection
				ftp_close($this->_stream);

				// reset stream
				$this->_stream = FALSE;
			}
		}
	}