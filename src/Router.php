<?php

namespace Mrfoo\PHPRouter;

use Mrfoo\PHPRouter\Core\LinkedList;
use Mrfoo\PHPRouter\Core\Route;
use Mrfoo\PHPRouter\Core\URI;
use Mrfoo\PHPRouter\Exceptions\MethodNotSupportedException;
use Exception;
use Mrfoo\PHPRouter\Core\HashTable;
use Mrfoo\PHPRouter\Exceptions\NotFoundException;

class Router {
	public static HashTable $routeList;
	public static ?HashTable $tmpGroup;
	public static bool $isTrackingGroup = false;
	public static Router $instance;

	/**
	 * Register a GET endpoint
	 *
	 * @param $uri
	 * @param $handler
	 *
	 * @return Route
	 */
	public static function get( $uri, $handler ): Route {
		return static::registerRoute( $uri, $handler, 'GET' );
	}


	public static function post( $uri, $handler ): Route {
		return static::registerRoute( $uri, $handler, 'POST' );
	}

	public static function put( $uri, $handler ): Route {
		return static::registerRoute( $uri, $handler, 'PUT' );
	}

	public static function patch( $uri, $handler ): Route {
		return static::registerRoute( $uri, $handler, 'PATCH' );
	}

	public static function delete( $uri, $handler ): Route {
		return static::registerRoute( $uri, $handler, 'DELETE' );
	}

	public static function match( array $methods, $uri, $handler ) {
		return die( 'unimplemented function: ' . __FUNCTION__ );
	}

	public static function any( $uri, $handler ) {
		return die( 'unimplemented function: ' . __FUNCTION__ );
	}

	public static function init(): void {
		if ( ! isset( self::$routeList ) ) {
			self::$routeList = new HashTable();
			self::$tmpGroup = new HashTable();
			self::$instance  = new Router();
		}
	}

	/**
	 * Run the router and begin handling the request
	 * @return bool
	 */
	public static function run(): bool {
		self::init();
		$user_uri    = parse_url( rawurldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH );
		$user_method = $_SERVER['REQUEST_METHOD'];
		$route       = self::$routeList->search( new Route($user_uri, null, $user_method) );

		if ( $route && $route->getMethod() == $user_method ) {
			try {
				// run user provided handler
				$route->handle();
				$route->postHandleMiddlewares();
			} catch ( Exception $e ) {
				echo "Exception occurred while handling: {$route->getURI()->getUri()}";
			}
		} else {
			try {
				self::$instance->performTests( $route, $user_uri, $user_method );
			} catch ( Exception $e ) {
				echo $e->getMessage();

				return false;
			}
		}

		return true;
	}

	public static function registerRoute( $uri, $handler, $method ): Route {
		$route = new Route( $uri, $handler, $method );

		self::init();
		if ( self::$isTrackingGroup ) {
			self::$tmpGroup->add( $route );
		} else {
			self::$routeList->add( $route );
		}

		return $route;
	}

	/**
	 * @param Route|null $route
	 * @param mixed $uri
	 * @param string $user_method
	 *
	 * @throws MethodNotSupportedException
	 * @throws NotFoundException
	 */
	private function performTests(?Route $route, mixed $uri, string $user_method ): void {
		if ( $route == null ) {
			throw new NotFoundException( $uri );
		}
		if ( $route->getMethod() != $user_method ) {
			throw new MethodNotSupportedException( $user_method, [ $route->getMethod() ] );
		}
	}

	public static function group( array $options, $callback ): void {
		self::init();
		self::$isTrackingGroup = true;

		if ( $options && count( $options ) > 0 ) {
			$_p = $options['prefix'] ?? false;
			$_m = $options['middleware'] ?? false;

			if ( $callback ) {
				call_user_func( $callback );
			}

			// now $tmpGroup have each grouped route
			self::$tmpGroup->forEach( function ( Route $route ) use ( $_p, $_m ) {
				// 1. apply prefix
				if ( $_p ) {
					$route->applyPrefix( $_p );
				}
				// 2. apply middlewares
				if ( $_m ) {
					$route->middleware( $_m );
				}
			} );

		}

		self::$routeList->mergeAtTail( self::$tmpGroup );

		self::$isTrackingGroup = false;
		self::$tmpGroup        = null;
	}

	public static function redirect( $from, $to, $status = null ) {
		$handle = function () use ( $to, $status ) {

			if ( $status !== null ) {
				http_response_code( $status );
			} else {
				http_response_code( 302 );
			}

			header( 'Location: ' . $to );
		};

		self::registerRoute( $from, $handle, 'GET' );
	}

	public static function permanentRedirect( $from, $to ) {
		$handle = function () use ( $to ) {
			http_response_code( 301 );
			header( 'Location: ' . $to );
		};

		self::registerRoute( $from, $handle, 'GET' );
	}

	public static function generateURL( $routeName, ...$params ): string {
		// find the Route
		$route = self::$routeList->searchByName( $routeName );

		// map each route with each param
		if ( $route ) {
			$segments   = $route->getURI()->getSegments();
			$parameters = $route->getURI()->getParameters();

			if ( count( $params ) == count( $parameters ) ) {

				// construct the URL string
				foreach ( $parameters as $key => $p ) {
					$parameters[ $key ] = $params[ array_search( $key, array_keys( $parameters ) ) ];
				}

				$base      = getBaseUrl();
				$uri       = [];
				$nextParam = 0;

				foreach ( $segments as $i => $seg ) {
					if ( $seg[0] == '{' ) {
						$uri[ $i ] = $parameters[ array_keys( $parameters )[ $nextParam ] ];
						$nextParam ++;
					} else {
						$uri[ $i ] = $seg;
					}
				}

				return $base . implode( "/", $uri );
			} else {
				return die( "Parameters provided for route '$routeName' do not match parameters count" );
			}
		} else {
			return die( "No route with name '$routeName'" );
		}
	}
}