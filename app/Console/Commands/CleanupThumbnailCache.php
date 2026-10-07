<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CleanupThumbnailCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:cleanup-thumbnails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired thumbnail cache yang tidak terpakai untuk menghemat disk space';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Membersihkan cache thumbnail yang expired...');

        // Laravel cache akan otomatis cleanup item yang expired
        // Command ini hanya trigger garbage collection
        
        // Untuk file cache driver, kita bisa manual scan dan hapus file expired
        if (config('cache.default') === 'file') {
            $cacheDir = storage_path('framework/cache/data');
            $count = 0;
            
            if (is_dir($cacheDir)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($cacheDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getExtension() === '') {
                        // Cek apakah file cache adalah thumbnail cache
                        $contents = @file_get_contents($file->getPathname());
                        if ($contents && strpos($contents, 'item-thumb:') !== false) {
                            // Parse expiration time dari cache file
                            $data = @unserialize($contents);
                            if (is_array($data) && isset($data[0]) && $data[0] < time()) {
                                // Cache expired, hapus
                                @unlink($file->getPathname());
                                $count++;
                            }
                        }
                    }
                }
            }

            $this->info("Berhasil membersihkan {$count} file cache thumbnail yang expired.");
        } else {
            // Untuk redis/memcached, cleanup otomatis
            $this->info('Cache driver ' . config('cache.default') . ' akan otomatis cleanup expired items.');
        }

        return Command::SUCCESS;
    }
}
