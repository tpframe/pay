<?php

namespace GFL\Tool\Commands;

use DB;
use Excel;
use Schema;
use Illuminate\Console\Command;

class CreateTable54B extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gfl-seed:T54B';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create data Table 54B';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        do {
            $table = $this->ask('table 54B name');
        } while (!Schema::hasTable($table));

        do {
            $Tem = $this->ask('Column temperature name');
        } while (!Schema::hasColumn($table, $Tem));

        do {
            $D15 = $this->ask('Column density 15*c name');
        } while (!Schema::hasColumn($table, $D15));

        do {
            $VCF = $this->ask('Column VCF name');
        } while (!Schema::hasColumn($table, $VCF));

        $headers = ['table', 'temperature', 'density 15*c', 'VCF'];
        $info = [
            [$table, $Tem, $D15, $VCF]
        ];
        $this->info('Table info :');
        $this->table($headers, $info);

        if ($this->confirm('Do you want init data table ' . $table . ' ?')) {
            $path = "public//exels//t54b.csv";
            $data = Excel::load($path)->get();
            $bar = $this->output->createProgressBar(count($data));
            $this->line('init data ...');
            foreach ($data as $key => $value) {
                DB::table($table)->insert([
                    $Tem => $value->tem,
                    $D15 => $value->d15,
                    $VCF => $value->vcf,
                ]);
                $bar->advance();
            }
            $bar->finish();

            // $this->line('init success !!!');
        }
    }
}