<?php

namespace Payone\Payone\Api;

class Log {
	const TABLE_NAME = 'payone_api_log';

	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Response
	 */
	private $response;

	/**
	 * @var \DateTime
	 */
	private $createdAt;

	public function __construct( $id = null ) {
		if ( $id ) {
			$this->id = $id;
		}
		$this->setCreatedAt( new \DateTime() );
	}

	/**
	 * @param array $row
	 *
	 * @return Log
	 */
	public static function constructFromDatabase( $row ) {
		$object = new Log( $row['id'] );
		$object
			->set_request( Request::construct_from_json( $row['request'] ) )
			->setResponse( Response::construct_from_json( $row['response'] ) )
			->setCreatedAt( new \DateTime( $row['created_at'] ) );

		return $object;
	}

	public function save() {
		global $wpdb;

		$tableName = $wpdb->prefix . self::TABLE_NAME;

		$wpdb->insert(
			$tableName,
			[
				'request'    => $this->request->get_serialized_parameters(),
				'response'   => $this->response->get_serialized_parameters(),
				'created_at' => $this->getCreatedAt()->format( 'Y-m-d H:i:s' ),
			],
			[ '%s', '%s', '%s' ]
		);
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * @param Request $request
	 *
	 * @return Log
	 */
	public function set_request( $request ) {
		$this->request = $request;

		return $this;
	}

	/**
	 * @return Response
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @param Response $response
	 *
	 * @return Log
	 */
	public function setResponse( $response ) {
		$this->response = $response;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @param \DateTime $createdAt
	 *
	 * @return Log
	 */
	public function setCreatedAt( $createdAt ) {
		$this->createdAt = $createdAt;

		return $this;
	}
}