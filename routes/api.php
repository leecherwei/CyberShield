<?php

use App\Http\Controllers\Api\OrganisationApiController;
use Illuminate\Support\Facades\Route;

// Exposure route for WORAWICH (Module 3.1)
Route::post('/v1/organisation/profile', [OrganisationApiController::class, 'getOrganisationProfile']);
