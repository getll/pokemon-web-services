<?php

require_once './includes/app_constants.php';
require_once './models/BaseModel.php';
require_once './models/ArtistModel.php';
require_once './models/CustomerModel.php';
// TODO: don't forget to require the following files. 
require_once './helpers/Paginator.php';
require_once './helpers/WebServiceInvoker.php';
require_once './controllers/IceAndFireController.php';

require __DIR__ . '/vendor/autoload.php';

// Paginator-related test cases.
 testPaginator();
//testArtistModel();
//testCustomerModel();
//testRemoteWSCall();
//testCompositeResource();

/**
 * Creates a composite resource by combining two data sets from different
 * data sources. 
 */
function testCompositeResource() {
    $artists_and_books = Array();
    // Get books data from the Ice and Fire API.
    $iceAndFire = new IceAndFireController();
    $books = $iceAndFire->getBooksInfo();
    // Get the list of artists.    
    $artist_model = new ArtistModel();        
    $artists = $artist_model->getAll();
    // Combine the data sets.
    $artists_and_books["books"] = $books;
    $artists_and_books["artists"] = $artists;
    $jsonData = json_encode($artists_and_books, JSON_INVALID_UTF8_SUBSTITUTE);
    echo $jsonData;
}

function testRemoteWSCall() {
    $iceAndFire = new IceAndFireController();
    $books = $iceAndFire->getBooksInfo();
    // Add the list of books to another data set.
    $jsonBooks = json_encode($books, JSON_INVALID_UTF8_SUBSTITUTE);
    echo $jsonBooks;
}

/**
 * Tests the pagination operations on the ArtistModel.
 */
function testArtistModel() {
    $input_page_number = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $input_per_page = filter_input(INPUT_GET, "per_page", FILTER_VALIDATE_INT);
    // Instantiate the model.
    $artist_model = new ArtistModel();
    // Set the pagination options.
    $artist_model->setPaginationOptions($input_page_number, $input_per_page);
    // Tests for the methods exposed by the ArtistModel.
    //-------
    $artists = $artist_model->getAll();

    // Test paginating a result set with filtering operation.
    //$artists = $artist_model->getWhereLike("A");
    print_r(json_encode($artists));
    //$artists = $artist_model->getArtistById(7);
    //print_r(json_encode($artists));        
}

/**
 * Tests the pagination operations on the CustomerModel.
 */
function testCustomerModel() {
    $input_page_number = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $input_per_page = filter_input(INPUT_GET, "per_page", FILTER_VALIDATE_INT);

    // Set default values if one of the following was invalid.
    $page_number = ($input_page_number > 0) ? $input_page_number : 1;
    $per_page = ($input_per_page > 0) ? $input_per_page : 10;

    $customer_model = new CustomerModel();
    // Set the pagination options.
    $customer_model->setPaginationOptions($page_number, $per_page);
    // Tests for the methods exposed by the ArtistModel.
    //-------
    $customers = $customer_model->getAll();
    print_r(json_encode($customers));
}

/**
 * Test with the following URI: index.php?page=1&per_page=10
 * Tests the logic of the Paginator class.
 */
function testPaginator() {
    // Test with: http://localhost/pdo-wrapper/?page=21&per_page=15        
    $input_page_number = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $input_per_page = filter_input(INPUT_GET, "per_page", FILTER_VALIDATE_INT);

    // Set default values if one of the following was invalid.
    $page_number = ($input_page_number > 0) ? $input_page_number : 1;
    $per_page = ($input_per_page > 0) ? $input_per_page : 10;

    // Should be the number of records returned by an SQL query
    $total_no_of_records = 275;
    $paginator = new Paginator($page_number, $per_page, $total_no_of_records);

    echo "Paginator test results: <br>";
    echo "<hr/>";
    echo "Page number: " . $page_number . "<br>";
    echo "Offset: " . $paginator->getOffset() . "<br>";
    echo "Total number of pages: " . $paginator->getTotalPages() . "<br>";
    echo "Total number of records: " . $paginator->getTotalRecords() . "<br>";
    echo "<br> Pagination info: <br>";
    print_r($paginator->getPaginationInfo());
}
