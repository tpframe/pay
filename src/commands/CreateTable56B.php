<?php

namespace GFL\Tool\Commands;

use DB;
use Excel;
use Schema;
use Illuminate\Console\Command;

class CreateTable56B extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gfl-seed:T56B';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create data Table 56B';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        do {
            $table = $this->ask('table 56B name');
        } while (!Schema::hasTable($table));

        do {
            $D15 = $this->ask('Column density 15*c name');
        } while (!Schema::hasColumn($table, $D15));

        do {
            $WCF = $this->ask('Column WCF name');
        } while (!Schema::hasColumn($table, $WCF));

        $headers = ['table', 'density 15*c', 'WCF'];
        $info = [
            [$table, $D15, $WCF]
        ];
        $this->info('Table info :');
        $this->table($headers, $info);

        if ($this->confirm('Do you want init data table ' . $table . ' ?')) {
            $path = "public//exels//t56b.csv";
            $data = Excel::load($path)->get();
            $bar = $this->output->createProgressBar(count($data),1);
            $this->line('init data ...');
            foreach ($data as $key => $value) {
                DB::table($table)->insert([
                    $D15 => $value->d15,
                    $WCF => $value->wcf,
                ]);
                $bar->advance();
            }
            $bar->finish();
            // $this->line('init success !!!');
        }
    }
}