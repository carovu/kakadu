<?php

require_once 'TestCaseCourse.php';

class CatalogWithPermissionsTest extends TestCaseCourse {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = null;
    private $catalog = null;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $this->catalog = $this->course->catalog()->first();

        //Login in
        $user = Sentry::findUserByLogin('alex@example.com');
        Sentry::login($user, false);
    }
    public function tearDown() {
        parent::tearDown();
    }

    /**
     * Test the view to show a existing catalog
     */
    public function testCatalogViewExistingID() {
        $response = $this->call('GET', 'api/v1/catalog/' . $this->catalog->id);
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test the view to show a not existing catalog
     */
    public function testCatalogViewNotExistingID() {
        $id = $this->getNotExistingID('Catalog');
        $response = $this->call('GET', 'api/v1/catalog/' . $id);
        $this->assertEquals('404', $response->getStatusCode());
    }


    /**
     * Test the view to create a catalog
     */
    public function testCatalogCreateView() {
        $response = $this->call('GET', 'api/v1/course/' . $this->course->id . '/catalog/create');
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test create catalog with a too short data
     *
     * @depends testCatalogCreateView
     */
    public function testCatalogCreatePostWithTooShortData() {
        $post_data = array(
            'course' => $this->course->id,
            'name'   => '',
            'number' => '',
            'parent' => ''
        );
        $response = $this->call('POST', 'api/v1/catalog/create', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('course//catalog/create', $response);
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test create catalog with not valid parent
     *
     * @depends testCatalogCreatePostWithTooShortData
     */
    public function testCatalogCreatePostWithNotValidParent() {
        $parent = Catalog::where('name', 'LIKE', 'Catalog of course 2 -  group Test xy - chapter 1')->first();

        $post_data = array(
            'course' => $this->course->id,
            'name'   => 'Test yz',
            'number' => '1',
            'parent' => $parent->id
        );
        $response = $this->call('POST', 'api/v1/catalog/create', $post_data);

        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('course/' . $this->course->id . '/catalog/create', $response);
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test create catalog with valid data
     *
     * @depends testCatalogCreatePostWithNotValidParent
     */
    public function testCatalogCreatePostWithValidData() {
        $post_data = array(
            'course' => $this->course->id,
            'name'   => 'Catalog of course Test xy - Chapter xy',
            'number' => '4',
            'parent' => $this->catalog->id
        );
        $response = $this->call('POST', 'api/v1/catalog/create', $post_data);

        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('catalog/[0-9]+', $response);
        //$this->checkIfNoErrorsExist();

        //Check the catalog
        $catalog = Catalog::where('name', 'LIKE', 'Catalog of course Test xy - Chapter xy')->first();
        $this->assertNotNull($catalog);
        $this->assertEquals(4, $catalog->number);
        $this->assertEquals($this->catalog->id, $catalog->parent);
    }


    /**
     * Test the view to edit a existing catalog
     */
    public function testCatalogEditWithExistingID() {
        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();
        $response = $this->call('GET', 'api/v1/catalog/'. $subcatalog->id . '/edit');
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test the view to edit a not existing catalog
     */
    public function testCatalogEditWithNotExistingID() {
        $id = $this->getNotExistingID('Catalog');
        $response = $this->call('GET', 'api/v1/catalog/' . $id . '/edit');
        $this->assertEquals('404', $response->getStatusCode());
    }


    /**
     * Test edit course with not existing id
     */
    public function testCatalogEditPostWithNotExistingID() {
        $id = $this->getNotExistingID('Catalog');

        $post_data = array(
            'course' => $this->course->id,
            'id'     => $id,
            'name'   => 'Catalog of course Test xy - Chapter xy',
            'number' => '4',
            'parent' => $this->catalog->id
        );
        $response = $this->call('POST', 'api/v1/catalog/edit', $post_data);
        $this->assertEquals('404', $response->getStatusCode());
    }


    /**
     * Test edit catalog with too short data
     *
     * @depends testCatalogEditPostWithNotExistingID
     */
    public function testCatalogEditPostWithTooShortData() {
        $id = $this->getNotExistingID('Catalog');
        $post_data = array(
            'course' => '',
            'id'     => $id,
            'name'   => '',
            'number' => '',
            'parent' => ''
        );
        $response = $this->call('POST', 'api/v1/catalog/edit', $post_data);

        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('catalog//edit', $response);
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test edit catalog with a catalog not a part of the course
     *
     * @depends testCatalogEditPostWithTooShortData
     */
    public function testCatalogEditPostWithNotCatalogOfCourse() {
        $other = Catalog::where('name', 'LIKE', 'Catalog of course 2 -  group Test xy - chapter 1')->first();

        $post_data = array(
            'course' => $this->course->id,
            'id'     => $other->id,
            'name'   => 'Test yz',
            'number' => '1',
            'parent' => $this->catalog->id
        );
        $response = $this->call('POST', 'api/v1/catalog/edit', $post_data);

        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('catalog/' . $other->id . '/edit', $response);
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test edit catalog with a parent catalog not part of the course
     *
     * @depends testCatalogEditPostWithNotCatalogOfCourse
     */
    public function testCatalogEditPostWithNotParentCatalogOfCourse() {
        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();
        $other = Catalog::where('name', 'LIKE', 'Catalog of course 2 -  group Test xy - chapter 1')->first();

        $post_data = array(
            'course' => $this->course->id,
            'id'     => $subcatalog->id,
            'name'   => 'Test yz',
            'number' => '1',
            'parent' => $other->id
        );
        $response = $this->call('POST', 'api/v1/catalog/edit', $post_data);

        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('catalog/' . $subcatalog->id . '/edit', $response);
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test edit catalog with a not editable catalog
     *
     * @depends testCatalogEditPostWithNotParentCatalogOfCourse
     */
    public function testCatalogEditPostWithNotEditableCatalog() {
        $post_data = array(
            'course' => $this->course->id,
            'id'     => $this->catalog->id,
            'name'   => 'Test yz',
            'number' => '1',
            'parent' => $this->catalog->id
        );
        $response = $this->call('POST', 'api/v1/catalog/edit', $post_data);

        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('catalog/' . $this->catalog->id . '/edit', $response);
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test edit catalog with valid data
     *
     * @depends testCatalogEditPostWithNotEditableCatalog
     */
    public function testCatalogEditPostWithValidData() {
        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();

        $post_data = array(
            'course' => $this->course->id,
            'id'     => $subcatalog->id,
            'name'   => 'Test yz',
            'number' => '1',
            'parent' => $this->catalog->id
        );

        $response = $this->call('POST', 'api/v1/catalog/edit', $post_data);

        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('catalog/' . $subcatalog->id, $response);
        //$this->checkIfNoErrorsExist();

        //Check the catalog
        $catalog = Catalog::where('name', 'LIKE', 'Test yz')->first();
        $this->assertNotNull($catalog);
        $this->assertEquals(1, $catalog->number);
        $this->assertEquals($this->catalog->id, $catalog->parent);
    }


    /**
     * Test delete catalog with not an existing course
     */
    public function testCatalogDeleteNotExistingID() {
        $id = $this->getNotExistingID('Catalog');
        $response = $this->call('GET', 'api/v1/catalog/' . $id . '/delete');
        $this->assertEquals('404', $response->getStatusCode());
    }


    /**
     * Test delete catalog with not an existing course
     *
     * @depends testCatalogDeleteNotExistingID
     */
    public function testCatalogDeleteWithNotEditableCatalog() {
        $response = $this->call('GET', 'api/v1/catalog/' . $this->catalog->id . '/delete');

        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('catalog/' . $this->catalog->id, $response);
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test delete catalog with valid data
     *
     * @depends testCatalogDeleteWithNotEditableCatalog
     */
    public function testCatalogDeleteWithValidData() {
        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();

        $response = $this->call('GET', 'api/v1/catalog/' . $subcatalog->id . '/delete');
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('course/' . $this->course->id, $response);

        $parent = Catalog::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $this->assertCount(4, $parent->questions()->get());
    }


    /**
     * Test the view to show a catalog was deleted
     */
    public function testCatalogDeletedView() {
        $response = $this->call('GET', 'api/v1/catalog/deleted');
        //$this->assertEquals('200', $response->getStatusCode());
    }

}