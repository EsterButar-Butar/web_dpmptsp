<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Parse formatted number string into float.
     * 
     * @param mixed $value
     * @return float
     */
    protected function parseNumber($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            return (float) $value;
        }

        return 0.0;
    }

    /**
     * Normalize keys by removing spaces and underscores, and lowercasing them.
     */
    protected function normalizeKeys(array $item)
    {
        $normalized = [];
        foreach ($item as $key => $value) {
            $cleanKey = strtolower(preg_replace('/[^a-z0-9]/i', '', $key));
            $normalized[$cleanKey] = $value;
        }
        return $normalized;
    }

    /**
     * Resolve Provinsi and Kabupaten names from import item.
     * Supports both names and regional codes using data_wilayah table.
     * 
     * @param array $item
     * @return array [provinsi, kabupaten]
     */
    protected function resolveRegionNames(array $item)
    {
        $norm = $this->normalizeKeys($item);
        $provinsiInput = $norm['provinsi'] ?? $norm['kodeprovinsi'] ?? $norm['kodewilayah'] ?? '';
        $kabupatenInput = $norm['kabupatenkota'] ?? $norm['kodekabupaten'] ?? $norm['kabupaten'] ?? '-';

        // Bersihkan input jika berupa string
        if (is_string($provinsiInput)) {
            $provinsiInput = trim($provinsiInput);
        }
        if (is_string($kabupatenInput)) {
            $kabupatenInput = trim($kabupatenInput);
        }

        // 1. Jika Provinsi berupa kode wilayah (angka, e.g. 12 atau 12.00)
        if ($provinsiInput !== '' && (is_numeric($provinsiInput) || preg_match('/^[0-9.]+$/', (string)$provinsiInput))) {
            $code = (string)$provinsiInput;
            $parts = explode('.', $code);
            $provCode = $parts[0];
            
            $wilayah = \App\Models\DataWilayah::where('kode_provinsi', $provCode)
                ->orWhere('kode_provinsi', $code)
                ->first();
                
            if ($wilayah) {
                $provinsiInput = $wilayah->nama_provinsi;
            }
        }

        // 2. Jika Kabupaten/Kota berupa kode wilayah (angka, e.g. 1271 atau 12.71)
        if ($kabupatenInput !== '-' && $kabupatenInput !== '' && (is_numeric($kabupatenInput) || preg_match('/^[0-9.]+$/', (string)$kabupatenInput))) {
            $code = (string)$kabupatenInput;
            
            $wilayah = \App\Models\DataWilayah::where('kode_kabupaten', $code)
                ->first();
                
            if ($wilayah) {
                $provinsiInput = $wilayah->nama_provinsi; // Update provinsi agar sinkron
                $kabupatenInput = $wilayah->nama_kabupaten;
            }
        }

        // Fallback default jika masih kosong atau tidak valid
        if (empty($provinsiInput)) {
            $provinsiInput = 'Sumatera Utara';
        }
        if (empty($kabupatenInput)) {
            $kabupatenInput = '-';
        }

        return [
            'provinsi' => $provinsiInput,
            'kabupaten' => $kabupatenInput
        ];
    }

    /**
     * Resolve Sektor name from import item.
     * Supports both name and sector ID.
     * 
     * @param array $item
     * @return string
     */
    protected function resolveSektorName(array $item)
    {
        $norm = $this->normalizeKeys($item);
        $sektorInput = $norm['sektor'] ?? $norm['sektorid'] ?? $norm['kodesektor'] ?? $norm['namasektor'] ?? '';
        
        if (is_string($sektorInput)) {
            $sektorInput = trim($sektorInput);
        }

        // Jika berupa angka (ID sektor, misal: 1, 2, 3)
        if ($sektorInput !== '' && is_numeric($sektorInput)) {
            $sektor = \App\Models\Sektor::find(intval($sektorInput));
            if ($sektor) {
                return $sektor->nama_sektor;
            }
        }

        return $sektorInput ?: 'PERTANIAN, KEHUTANAN, DAN PERIKANAN'; // Default fallback
    }
}
