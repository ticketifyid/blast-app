<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignDispatchController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactGroupController;
use App\Http\Controllers\ExcelHeaderController;
use App\Http\Controllers\PhonebookImportController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('campaigns.index');
});

// Config
Route::get('/config', [ConfigController::class, 'index'])->name('config.index');
Route::post('/config', [ConfigController::class, 'store'])->name('config.store');

// Contacts
Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

// Contact Groups
Route::get('/contact-groups', [ContactGroupController::class, 'index'])->name('contact-groups.index');
Route::post('/contact-groups', [ContactGroupController::class, 'store'])->name('contact-groups.store');
Route::get('/contact-groups/{contactGroup}', [ContactGroupController::class, 'show'])->name('contact-groups.show');
Route::delete('/contact-groups/{contactGroup}', [ContactGroupController::class, 'destroy'])->name('contact-groups.destroy');

// Phonebook Import
Route::post('/phonebook/upload', [PhonebookImportController::class, 'upload'])->name('phonebook.upload');
Route::post('/phonebook/import', [PhonebookImportController::class, 'import'])->name('phonebook.import');

// Templates
Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
Route::get('/templates/create', [TemplateController::class, 'create'])->name('templates.create');
Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');
Route::get('/templates/{template}/edit', [TemplateController::class, 'edit'])->name('templates.edit');
Route::put('/templates/{template}', [TemplateController::class, 'update'])->name('templates.update');
Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');

// Campaigns
Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
Route::post('/campaigns/{campaign}/dispatch', CampaignDispatchController::class)->name('campaigns.dispatch');

// Excel Headers
Route::post('/excel/headers', ExcelHeaderController::class)->name('excel.headers');
