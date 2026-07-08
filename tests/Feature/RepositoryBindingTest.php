<?php

declare(strict_types=1);

use App\Repositories\ClientRepository;
use App\Repositories\ContactRepository;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\ContactRepositoryInterface;
use App\Repositories\Contracts\ProjectLinkRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\ProjectLinkRepository;
use App\Repositories\ProjectRepository;
use App\Services\ClientService;
use App\Services\ContactService;
use App\Services\ProjectLinkService;
use App\Services\ProjectService;

it('resolves each repository interface to its concrete implementation', function (string $interface, string $concrete) {
    expect(app($interface))->toBeInstanceOf($concrete);
})->with([
    'client' => [ClientRepositoryInterface::class, ClientRepository::class],
    'contact' => [ContactRepositoryInterface::class, ContactRepository::class],
    'project' => [ProjectRepositoryInterface::class, ProjectRepository::class],
    'projectLink' => [ProjectLinkRepositoryInterface::class, ProjectLinkRepository::class],
]);

it('resolves each service through the container', function (string $service) {
    expect(app($service))->toBeInstanceOf($service);
})->with([
    ClientService::class,
    ContactService::class,
    ProjectService::class,
    ProjectLinkService::class,
]);
