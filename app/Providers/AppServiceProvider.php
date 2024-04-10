<?php

namespace App\Providers;

use App\Classes\Enums\ParserSource;
use App\Classes\Factories\ParserFactory;
use App\Classes\Parsers\IRosterParser;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IRosterParser::class, function ($app) {
            $sourceType = $app->get('request')->source_type;
            $sourceData = $app->get('request')->source_data;

            $parser = (new ParserFactory(ParserSource::from($sourceType)))->make($sourceData);

            if ($parser === null) {
                throw new BindingResolutionException("Could not resolve a parser for given source: " . $sourceType);
            }

            return $parser;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setTestNow(Carbon::createFromDate(2022, 1, 14, 'UTC'));
    }
}
