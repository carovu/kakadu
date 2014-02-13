<?php

require_once 'TestCaseCourse.php';

class CatalogWithoutPermissionsTest extends TestCaseCourse {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = null;
    private $catalog = null;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $this->catalog = $this->course->catalog()->first();
    }
    
    public function tearDown() {
        parent::tearDown();
    }

    /**
     * Test the view to show a existing catalog
     */
    public function testCatalogViewExistingID() {
        $response = $this->call('GET', 'catalog/' . $this->catalog->id);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->original);
    }


    /**
     * Test the view to create a catalog
     */
    public function testCatalogCreateView() {
        $response = $this->call('GET', 'course/' . $this->course->id . '/catalog/create');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test create catalog with valid data
     */
    public function testCatalogCreatePostWithValidData() {
        $post_data = array(
            'course' => $this->course->id,
            'name'   => 'Catalog of course Test xy - Chapter xy',
            'number' => '4',
            'parent' => $this->catalog->id
        );
        $response = $this->call('POST', 'catalog/create', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test the view to edit a existing catalog
     */
    public function testCatalogEditWithExistingID() {
        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();
        $response = $this->call('GET', 'catalog/'. $subcatalog->id . '/edit');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test edit catalog with valid data
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
        $response = $this->call('POST', 'catalog/edit', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test delete catalog with valid data
     */
    public function testCatalogDeleteWithValidData() {
        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();

        $response = $this->call('GET', 'catalog/' . $subcatalog->id . '/delete');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }

}