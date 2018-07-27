<?php

namespace GFL\Tool\Commands;

use DB;
use Excel;
use Schema;
use Illuminate\Console\Command;

class CreateTable53B extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gfl-seed:T53B';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create data Table 53B';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        do {
            $table = $this->ask('table 53B name');
        } while (!Schema::hasTable($table));

        do {
            $Tem = $this->ask('Column temperature name');
        } while (!Schema::hasColumn($table, $Tem));

        do {
            $Dtt = $this->ask('Column density actual name');
        } while (!Schema::hasColumn($table, $Dtt));

        do {
            $D15 = $this->ask('Column density 15*c name');
        } while (!Schema::hasColumn($table, $D15));

        $headers = ['table', 'temperature', 'density actual', 'density 15*c'];
        $info = [
            [$table, $Tem, $Dtt, $D15]
        ];
        $this->info('Table info :');
        $this->table($headers, $info);

        if ($this->confirm('Do you want init data table ' . $table . ' ?')) {
            $path = "public//exels//t53b.csv";
            $data = Excel::load($path)->get();
            $bar = $this->output->createProgressBar(count($data));
            $this->line('init data ...');
            foreach ($data as $key => $value) {
                DB::table($table)->insert([
                    $Tem => $value->tem,
                    $Dtt => $value->dtt,
                    $D15 => $value->d15,
                ]);
                $bar->advance();
            }
            $bar->finish();

            // $this->line('init success !!!');
        }

    }
}
