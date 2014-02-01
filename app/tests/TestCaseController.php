<?php
use Illuminate\Routing\UrlGenerator;

abstract class TestCaseController extends TestCase
{

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        Sentry::logout();
        Session::flush();

    }

    /**
     * Checks if the response has the right destination uri
     * @param  string $destination
     * @param  string $response
     */
    protected function checkResponseLocation($destination, $response) {
        $this->assertRegExp("/^#[http://:/]#" . $destination . "$#", $response->headers->get('location'));
    }
}
