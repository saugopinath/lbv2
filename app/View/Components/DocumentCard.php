<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DocumentCard extends Component
{
    public $doc;
    public $existingDocuments;

    public function __construct($doc, $existingDocuments)
    {
        $this->doc = $doc;
        $this->existingDocuments = $existingDocuments;
    }

    public function render()
    {
        return view('components.document-card');
    }
}

