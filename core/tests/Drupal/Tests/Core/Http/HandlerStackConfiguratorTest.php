<?php

/**
 * @file
 * Contains \Drupal\Tests\Core\Http\HandlerStackConfiguratorTest.
 */

namespace Drupal\Tests\Core\Http;

use Drupal\Core\Http\HandlerStackConfigurator;
use Drupal\Tests\UnitTestCase;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * @coversDefaultClass \Drupal\Core\Http\HandlerStackConfigurator
 * @group http
 */
class HandlerStackConfiguratorTest extends UnitTestCase {

  /**
   * @covers ::configure
   * @covers ::initializeMiddlewares
   *
   * @expectedException \InvalidArgumentException
   */
  public function testConfigureWithNonCallableService() {
    $handler_stack = new HandlerStack();

    $container = new Container();
    $container->set('test_middleware_non_callable', new TestMiddlewareNonCallable());
    $middleware_ids = ['test_middleware_non_callable'];
    $handler_stack_configurator = new HandlerStackConfigurator($container, $middleware_ids);
    $handler_stack_configurator->configure($handler_stack);
  }

  /**
   * @covers ::configure
   * @covers ::initializeMiddlewares
   */
  public function testConfigureWithCallableService() {
    $handler_stack = new HandlerStack(new CurlHandler());

    $container = new Container();
    $container->set('test_middleware_callable', new TestMiddlewareCallable());
    $middleware_ids = ['test_middleware_callable'];
    $handler_stack_configurator = new HandlerStackConfigurator($container, $middleware_ids);
    $handler_stack_configurator->configure($handler_stack);

    $request = new Request('GET', '/example');
    $options = [];
    /** @var \Psr\Http\Message\ResponseInterface $response */
    $response = $handler_stack($request, $options);
    $this->assertEquals(418, $response->getStatusCode());
  }

}

class TestMiddlewareNonCallable {

}

class TestMiddlewareCallable {

  public function __invoke() {
    return function ($handler) {
      return function (RequestInterface $request, array $options) use ($handler) {
        return new Response(418);
      };
    };
  }

}
