<?php

namespace Stylemix\Generators\Commands;

class EventCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Event class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Event';
}