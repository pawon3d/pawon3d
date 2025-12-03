<?php

use App\Models\Unit;

beforeEach(function () {
    // Ensure kilogram and gram units exist with proper conversion data
    $this->kg = Unit::updateOrCreate(
        ['alias' => 'kg'],
        [
            'name' => 'Kilogram',
            'alias' => 'kg',
            'group' => 'Berat',
            'base_unit_id' => null,
            'conversion_factor' => 1,
        ]
    );

    $this->gram = Unit::updateOrCreate(
        ['alias' => 'g'],
        [
            'name' => 'Gram',
            'alias' => 'g',
            'group' => 'Berat',
            'base_unit_id' => $this->kg->id,
            'conversion_factor' => 0.001,
        ]
    );

    $this->pcs = Unit::updateOrCreate(
        ['alias' => 'pcs'],
        [
            'name' => 'Pcs',
            'alias' => 'pcs',
            'group' => null,
            'base_unit_id' => null,
            'conversion_factor' => 1,
        ]
    );
});

test('kilogram has conversion ladder', function () {
    expect($this->kg->hasConversionLadder())->toBeTrue();
});

test('gram has conversion ladder', function () {
    expect($this->gram->hasConversionLadder())->toBeTrue();
});

test('pcs does not have conversion ladder', function () {
    expect($this->pcs->hasConversionLadder())->toBeFalse();
});

test('kilogram can auto convert to gram', function () {
    expect($this->kg->canAutoConvertTo($this->gram))->toBeTrue();
});

test('gram can auto convert to kilogram', function () {
    expect($this->gram->canAutoConvertTo($this->kg))->toBeTrue();
});

test('kilogram cannot auto convert to pcs', function () {
    expect($this->kg->canAutoConvertTo($this->pcs))->toBeFalse();
});

test('pcs cannot auto convert to kilogram', function () {
    expect($this->pcs->canAutoConvertTo($this->kg))->toBeFalse();
});

test('1 kilogram converts to 1000 gram', function () {
    $result = $this->kg->convertTo(1, $this->gram);
    expect($result)->toBe(1000.0);
});

test('500 gram converts to 0.5 kilogram', function () {
    $result = $this->gram->convertTo(500, $this->kg);
    expect($result)->toBe(0.5);
});

test('conversion factor from kilogram to gram is 1000', function () {
    $factor = $this->kg->getConversionFactorTo($this->gram);
    expect($factor)->toBe(1000.0);
});

test('conversion factor from gram to kilogram is 0.001', function () {
    $factor = $this->gram->getConversionFactorTo($this->kg);
    expect($factor)->toBe(0.001);
});

test('conversion factor returns null for units without conversion ladder', function () {
    $factor = $this->kg->getConversionFactorTo($this->pcs);
    expect($factor)->toBeNull();
});

test('kilogram is base unit', function () {
    expect($this->kg->isBaseUnit())->toBeTrue();
});

test('gram is not base unit', function () {
    expect($this->gram->isBaseUnit())->toBeFalse();
});

test('pcs is base unit because it has no group', function () {
    expect($this->pcs->isBaseUnit())->toBeTrue();
});
