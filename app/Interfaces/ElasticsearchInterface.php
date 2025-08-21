<?php

namespace App\Interfaces;


interface ElasticsearchInterface
{

    public function testConnection(): any;
    public function createIndex(string $index_name): any;
    public function populateIndex(string $index_name,array $data): any;
    public function verifyExists(string $index_name,string $id): any;
    public function bulkIndexData(string $index_name,array $data): any;
    public function getPaginatedIndexData(string $index_name,integer $page,integer $page_size): any;
    public function getIndexData(string $index_name,string $id): any;

}