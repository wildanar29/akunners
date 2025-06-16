<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSwagger extends Command
{
    protected $signature = 'swagger:generate';
    protected $description = 'Generate Swagger JSON from app/Docs';

    public function handle()
    {
        $docsPath = base_path('app/Docs');
        $outputPath = storage_path('api-docs/api-docs.json');

        if (!file_exists(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        // Tambahkan `php` di depan `vendor/bin/openapi`
        $command = "php vendor/bin/openapi --output $outputPath $docsPath";

        exec($command, $output, $status);

        if ($status === 0) {
            $this->info("Swagger docs generated to: $outputPath");
        } else {
            $this->error("Failed to generate Swagger docs.");
        }
    }

}
