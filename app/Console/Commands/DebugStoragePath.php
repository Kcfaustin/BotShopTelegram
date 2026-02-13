<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DebugStoragePath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Affiche les chemins de stockage pour le débogage en production.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('--- Chemins de débogage pour le stockage ---');

        $basePath = base_path();
        $this->line("<comment>Chemin de base de l'application (base_path) :</comment> {$basePath}");

        $storagePath = storage_path();
        $this->line("<comment>Chemin du dossier de stockage (storage_path) :</comment> {$storagePath}");

        $disk = 'local';
        $filePath = 'products/guide-automation.pdf';

        $this->info("\n--- Vérification d'un fichier spécifique ---");
        $this->line("<comment>Disque utilisé :</comment> {$disk}");
        $this->line("<comment>Chemin relatif du fichier :</comment> {$filePath}");

        $absolutePath = Storage::disk($disk)->path($filePath);
        $this->line("<comment>Chemin absolu calculé par Laravel :</comment> {$absolutePath}");

        $exists = Storage::disk($disk)->exists($filePath);
        $status = $exists ? '<info>TROUVÉ</info>' : '<error>NON TROUVÉ</error>';
        $this->line("<comment>Statut :</comment> {$status}");

        if (!$exists) {
            $this->line("\n<error>Action requise :</error> Assurez-vous que le fichier existe bien à l'emplacement ci-dessus et que les permissions sont correctes.");
        } else {
            $this->line("\n<info>Le fichier a été trouvé par Laravel. Le problème vient probablement d'ailleurs (permissions ?).</info>");
        }

        return 0;
    }
}
