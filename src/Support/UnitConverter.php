<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

final readonly class UnitConverter
{
    public function __construct(private Config $config)
    {
    }

    public function depthToDisplay(float $meters): float
    {
        return $this->config->convertLength() ? $meters * 3.28084 : $meters;
    }

    public function pressureToDisplay(float $bar): float
    {
        return $this->config->convertPressure() ? $bar * 14.5038 : $bar;
    }

    public function weightToDisplay(float $kilograms): float
    {
        return $this->config->convertWeight() ? $kilograms * 2.20462 : $kilograms;
    }

    public function temperatureToDisplay(float $celsius): float
    {
        return $this->config->convertTemperature() ? (($celsius * 9.0 / 5.0) + 32.0) : $celsius;
    }

    public function volumeToDisplay(float $liters): float
    {
        return $this->config->convertVolume() ? $liters * 0.0353147 : $liters;
    }

    public function depthLabel(): string
    {
        return $this->config->convertLength() ? 'ft' : 'm';
    }

    public function pressureLabel(): string
    {
        return $this->config->convertPressure() ? 'psi' : 'bar';
    }

    public function weightLabel(): string
    {
        return $this->config->convertWeight() ? 'lbs' : 'kg';
    }

    public function temperatureLabel(): string
    {
        return $this->config->convertTemperature() ? 'F' : 'C';
    }

    public function volumeLabel(): string
    {
        return $this->config->convertVolume() ? 'cuft' : 'l';
    }
}
