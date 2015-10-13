<?php namespace App\Model;

/**
 * Abstract Model Class
 *
 * @package Myanmar Election API Router App
 * @author 
 **/

use Hexcores\MongoLite\Connection;
use Hexcores\MongoLite\Query;

abstract class Model 
{
	protected $connection;

	protected $db;

	/**
	 * The collection associated with the model.
	 *
	 * @var string
	 */
	protected $collection;

	public function __construct() 
	{

		//$this->connect();

		$this->connection = Connection::instance();

		$this->collection = $this->setCollection($this->collection);

	}

	public function getConnection()
	{
		return $this->connection;
	}

	public function setCollection($collection)
	{
		return mongo_lite($collection);
	}

	public function getCollection()
	{
		return $this->collection;
	}

	private function connect()
	{
		Connection::connect(env('DB_HOST', 'localhost'), env('DB_PORT', '27017'), env('DB_DATABASE', 'mmelection'));
	}

	/**
	 * Magic method __call
	 */
	public function __call($method, $parameters)
	{
		$query = $this->getCollection();

		return call_user_func_array(array($query, $method), $parameters);
	}

	/**
	 * Magic method __callStatic for static call
	 */
	public static function __callStatic($method, $parameters)
	{
		$ins = new static;

		return call_user_func_array(array($ins, $method), $parameters);
	}



} // END class Model 