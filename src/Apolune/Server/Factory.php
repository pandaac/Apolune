<?php

namespace Apolune\Server;

use Illuminate\Contracts\Foundation\Application;
use Apolune\Contracts\Server\Factory as Contract;

class Factory implements Contract
{
    /**
     * Holds the server data.
     *
     * @var \StdClass
     */
    protected $data;

    /**
     * Holds the application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new Factory instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  string  $file
     * @return void
     */
    public function __construct(Application $app, $file)
    {
        $this->app = $app;
        $this->data = json_decode($app['files']->get($file));
    }

    /**
     * Get the server name.
     *
     * @return string
     */
    public function name()
    {
        return $this->data->name;
    }

    /**
     * Get all of the countries.
     *
     * @return \Illuminate\Support\Collection
     */
    public function countries()
    {
        return collect($this->data->countries);
    }

    /**
     * Get all creatures.
     *
     * @return \Illuminate\Support\Collection
     */
    public function creatures()
    {
        $creatures = $this->data->creatures;

        array_walk($creatures, function (&$creature) {
            $creature = $this->app->make('server.creature', [(array) $creature]);
        });

        return collect($creatures)->sortBy('name')->sort(function ($a, $b) {
            return $a->name() > $b->name();
        })->filter(function ($item) {
            return ! $item->hidden();
        });
    }

    /**
     * Get all of the genders.
     *
     * @return \Illuminate\Support\Collection
     */
    public function genders()
    {
        $genders = $this->data->genders;

        array_walk($genders, function (&$gender) {
            $gender = $this->app->make('server.gender', [(array) $gender]);
        });

        return collect($genders);
    }

    /**
     * Get all of the towns.
     *
     * @param  boolean  $starter  null
     * @return \Illuminate\Support\Collection
     */
    public function towns($starter = null)
    {
        $towns = $this->data->towns;

        array_walk($towns, function (&$town) {
            $town = $this->app->make('server.town', [(array) $town]);
        });

        $collection = collect($towns)->reject(function ($town) use ($starter) {
            return $starter and ! $town->isStarter();
        });

        return $collection->count() > 0 ? $collection : $collection->push(head($towns));
    }

    /**
     * Get all of the vocations.
     *
     * @param  boolean  $starter  null
     * @return \Illuminate\Support\Collection
     */
    public function vocations($starter = null)
    {
        $vocations = $this->data->vocations;

        array_walk($vocations, function (&$vocation) {
            $vocation = $this->app->make('server.vocation', [(array) $vocation]);
        });

        $collection = collect($vocations)->reject(function ($vocation) use ($starter) {
            return $starter and ! $vocation->isStarter();
        });

        return $collection->count() > 0 ? $collection : $collection->push(head($vocations));
    }

    /**
     * Get all of the worlds.
     *
     * @return \Illuminate\Support\Collection
     */
    public function worlds()
    {
        $worlds = $this->data->worlds;

        array_walk($worlds, function (&$world) {
            $world = $this->app->make('server.world', [(array) $world]);
        });

        return collect($worlds);
    }
}
