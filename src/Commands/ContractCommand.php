<?php

namespace Stylemix\Generators\Commands;

class ContractCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Contract class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Contract';
}