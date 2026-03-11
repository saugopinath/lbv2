<?php

namespace App\Livewire;

use Livewire\Component;

use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CsvChunkExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CsvSplitter extends Component
{
    use WithFileUploads;

    public $csvFile;
    public $chunkSize = 1000000;
    public $isProcessing = false;
    public $progress = 0;
    public $files = [];
    public $error;

    // State for batching
    public $savedFilePath;
    public $originalFileName;
    public $batchId;
    public $currentRow = 0;
    public $totalRows = 0;
    public $currentFileIndex = 1;
    public $headers = [];

    public function process()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        $this->validate([
            'csvFile' => 'required|mimes:csv,txt|max:102400', // 100MB max
            'chunkSize' => 'required|integer|min:1|max:5000000',
        ]);

        $this->isProcessing = true;
        $this->progress = 0;
        $this->files = [];
        $this->error = null;
        $this->currentRow = 0;
        $this->currentFileIndex = 1;

        try {
            $this->originalFileName = Str::slug(pathinfo($this->csvFile->getClientOriginalName(), PATHINFO_FILENAME));
            $this->batchId = Str::random(5);
            $outputDir = "csv-splits-new/{$this->originalFileName}_{$this->batchId}";
            Storage::disk('public')->makeDirectory($outputDir);

            // Save the file permanently as temporary files are cleared
            $this->savedFilePath = $this->csvFile->storeAs($outputDir, 'source.csv', 'public');

            $path = Storage::disk('public')->path($this->savedFilePath);

            // Read headers and count total lines (roughly)
            $handle = fopen($path, 'r');
            if ($handle === false) {
                throw new \Exception("Could not open the uploaded file.");
            }
            $this->headers = fgetcsv($handle);

            $lines = 0;
            while (!feof($handle)) {
                $lines += substr_count(fread($handle, 8192), "\n");
            }
            $this->totalRows = $lines; // Note: approximate but good enough for progress
            fclose($handle);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->isProcessing = false;
        }
    }

    public function processBatch()
    {
        if (!$this->isProcessing || !$this->savedFilePath) return;

        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        try {
            $path = Storage::disk('public')->path($this->savedFilePath);
            $handle = fopen($path, 'r');

            if ($handle === false) {
                throw new \Exception("Could not open the saved source file.");
            }

            // Skip headers on first read, or skip to current row
            fgetcsv($handle); // Always skip header row

            // Seek to the current row
            $skipped = 0;
            while ($skipped < $this->currentRow && !feof($handle)) {
                fgetcsv($handle);
                $skipped++;
            }

            $currentChunkData = [];
            $rowCount = 0;

            // Read up to chunkSize
            while (($row = fgetcsv($handle)) !== false && $rowCount < $this->chunkSize) {
                // Ignore completely empty rows
                if (empty(array_filter($row))) continue;

                $currentChunkData[] = $row;
                $rowCount++;
            }

            fclose($handle);

            if ($rowCount > 0) {
                $baseName = Str::slug($this->originalFileName ?? 'export');
                $fileName = "{$baseName}_part_{$this->currentFileIndex}.xlsx";
                $outputDir = "csv-splits/{$this->originalFileName}_{$this->batchId}";
                $fileRelativePath = "{$outputDir}/{$fileName}";

                Excel::store(new CsvChunkExport($currentChunkData, $this->headers), $fileRelativePath, 'public');

                $this->files[] = [
                    'name' => $fileName,
                    'url' => Storage::url($fileRelativePath)
                ];

                $this->currentRow += $rowCount;
                $this->currentFileIndex++;

                if ($this->totalRows > 0) {
                    $this->progress = min(99, round(($this->currentRow / $this->totalRows) * 100));
                }
            } else {
                // We reached the end
                $this->isProcessing = false;
                $this->progress = 100;

                // Clean up source file
                Storage::disk('public')->delete($this->savedFilePath);
            }
        } catch (\Exception $e) {
            $this->error = 'Batch Error: ' . $e->getMessage() . ' line: ' . $e->getLine();
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.csv-splitter');
    }
}
