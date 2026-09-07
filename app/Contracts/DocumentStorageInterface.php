<?php

namespace App\Contracts;

interface DocumentStorageInterface
{
    public function uploadDocument($filePath, $fileName, $createdBy = null);
    public function downloadDocument($documentId);
    public function deleteDocument($documentId);
}
