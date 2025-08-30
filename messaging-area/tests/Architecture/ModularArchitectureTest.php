<?php

arch('controllers should not have private methods')
    ->expect('App\Modules\Messaging\Controllers')
    ->not->toHavePrivateMethods()
    ->ignoring('__construct');

arch('controllers should extend base controller')
    ->expect('App\Modules\Messaging\Controllers')
    ->toExtend('App\Http\Controllers\Controller');

arch('controllers should not use facades directly')
    ->expect('App\Modules\Messaging\Controllers')
    ->not->toUse(['Illuminate\Support\Facades\DB', 'Illuminate\Support\Facades\Cache'])
    ->ignoring('Illuminate\Support\Facades\Log');

arch('services should not depend on HTTP layer')
    ->expect('App\Modules\Messaging\Services')
    ->not->toUse([
        'Illuminate\Http\Request',
        'Illuminate\Http\JsonResponse',
        'Symfony\Component\HttpFoundation\Response'
    ]);

arch('services should use dependency injection')
    ->expect('App\Modules\Messaging\Services')
    ->toHaveConstructor();

arch('models should use proper traits and interfaces')
    ->expect('App\Modules\Messaging\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->toUse('Illuminate\Database\Eloquent\Factories\HasFactory');

arch('repositories should follow repository pattern')
    ->expect('App\Modules\Messaging\Repositories')
    ->toHaveSuffix('Repository')
    ->toHaveMethod('create');

arch('enums should be in Enums namespace')
    ->expect('App\Modules\Messaging\Enums')
    ->toBeEnums()
    ->toHaveSuffix('Enum');

arch('channels should implement ChannelInterface')
    ->expect('App\Modules\Messaging\Channels')
    ->toImplement('App\Modules\Messaging\Concerns\ChannelInterface')
    ->toHaveSuffix('Channel');

arch('extractors should be in correct namespace')
    ->expect('App\Modules\Messaging\Extractors')
    ->toHaveSuffix('Extractor')
    ->toHaveMethod('extract');

arch('no global functions should be used in services')
    ->expect('App\Modules\Messaging\Services')
    ->not->toUse(['dd', 'dump', 'ray']);

arch('no debugging functions in production code')
    ->expect('App\Modules')
    ->not->toUse(['dd', 'dump', 'var_dump', 'print_r']);
