<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class ServiceMakeCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $className = Str::studly($name);
        $modelName = Str::singular(str_replace('Service', '', $className));
        $tableName = Str::plural(Str::snake($modelName));

        $path = app_path('Services/' . $className . '.php');
        $filesystem = new Filesystem();

        if ($filesystem->exists($path)) {
            $this->error($className . ' already exists!');
            return;
        }

        // 模板内容
        $stub = file_get_contents( base_path( 'stubs\service.plain.stub' ) );

        $stub = str_replace(
            ['{{ class }}', '{{ Modal }}', '{{ modal }}', '{{ Modals }}', '{{ modals }}', '{{ table }}'],
            [$className, $modelName, Str::camel($modelName), Str::plural( $modelName ), Str::camel( Str::plural( $modelName ) ), $tableName],
            $stub
        );

        $filesystem->put($path, $stub);

        $this->info($className . ' created successfully.');
    }
}
