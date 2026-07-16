const XLSX = require('xlsx');
const fs = require('fs');

try {
    const workbook = XLSX.readFile('test_import_sektoral.xlsx');
    const firstSheet = workbook.SheetNames[0];
    const worksheet = workbook.Sheets[firstSheet];
    
    const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
    const headers = rows[0].map(h => String(h || '').trim().toLowerCase());
    const cleanHeaders = headers.map(h => h.replace(/[\s_-]/g, ''));
    
    let provIdx = -1;
    let kabIdx = -1;
    let sektorIdx = -1;
    let tahunIdx = -1;
    let nilaiProvIdx = -1;
    let nilaiKabIdx = -1;
    
    headers.forEach((h, idx) => {
        const cleanHeader = h.replace(/[\s_-]/g, '');
        if (provIdx === -1 && (cleanHeader === 'namaprovinsi' || cleanHeader === 'provinsi')) provIdx = idx;
        if (kabIdx === -1 && (cleanHeader === 'namakabupaten' || cleanHeader === 'kabupaten' || cleanHeader === 'kabupatenkota')) kabIdx = idx;
        if (sektorIdx === -1 && (cleanHeader === 'namasektor' || cleanHeader === 'sektor' || cleanHeader === 'namakategori')) sektorIdx = idx;
        if (tahunIdx === -1 && cleanHeader === 'tahun') tahunIdx = idx;
    });
    
    if (provIdx === -1) {
        headers.forEach((h, idx) => {
            const cleanHeader = h.replace(/[\s_-]/g, '');
            if (provIdx === -1 && (cleanHeader.includes('provinsiid') || cleanHeader.includes('kodeprovinsi') || cleanHeader.includes('kodewilayah'))) provIdx = idx;
        });
    }
    if (kabIdx === -1) {
        headers.forEach((h, idx) => {
            const cleanHeader = h.replace(/[\s_-]/g, '');
            if (kabIdx === -1 && (cleanHeader.includes('kabid') || cleanHeader.includes('kodekabupaten'))) kabIdx = idx;
        });
    }
    if (sektorIdx === -1) {
        headers.forEach((h, idx) => {
            const cleanHeader = h.replace(/[\s_-]/g, '');
            if (sektorIdx === -1 && (cleanHeader.includes('idsektor') || cleanHeader.includes('sektorid') || cleanHeader.includes('kodesektor'))) sektorIdx = idx;
        });
    }
    
    headers.forEach((h, idx) => {
        const cleanHeader = h.replace(/[\s_-]/g, '');
        if (nilaiProvIdx === -1 && (cleanHeader.includes('nilaiprov') || cleanHeader.includes('nilaiprdbprov') || cleanHeader.includes('nilaipdrbprov') || cleanHeader.includes('pdrbprov') || cleanHeader === 'nilaiprovinsi')) nilaiProvIdx = idx;
        if (nilaiKabIdx === -1 && (cleanHeader.includes('nilaikab') || cleanHeader.includes('nilaipdrbkab') || cleanHeader.includes('pdrbkab') || cleanHeader === 'nilaikabupaten')) nilaiKabIdx = idx;
    });
    
    if (nilaiProvIdx === -1 || nilaiKabIdx === -1) {
        const nilaiIndices = [];
        headers.forEach((h, idx) => {
            if (h.includes('nilai') || h.includes('pdrb')) nilaiIndices.push(idx);
        });
        if (nilaiIndices.length >= 2) {
            if (nilaiProvIdx === -1) nilaiProvIdx = nilaiIndices[0];
            if (nilaiKabIdx === -1) nilaiKabIdx = nilaiIndices[1];
        } else if (nilaiIndices.length === 1) {
            if (nilaiKabIdx === -1) nilaiKabIdx = nilaiIndices[0];
            if (nilaiProvIdx === -1) nilaiProvIdx = nilaiIndices[0];
        }
    }
    
    const parseNumberVal = (val) => {
        if (val === null || val === undefined) return 0;
        if (typeof val === 'number') return val;
        let str = String(val).trim();
        if (!str) return 0;
        str = str.replace(/\./g, '').replace(/,/g, '.');
        const parsed = parseFloat(str);
        return isNaN(parsed) ? 0 : parsed;
    };
    
    const dataRows = [];
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        if (!row || row.length === 0) continue;
        const provVal = provIdx !== -1 ? String(row[provIdx] || '').trim() : '';
        const kabVal = kabIdx !== -1 ? String(row[kabIdx] || '').trim() : '';
        const sektorVal = sektorIdx !== -1 ? String(row[sektorIdx] || '').trim() : '';
        const tahunVal = tahunIdx !== -1 ? parseInt(row[tahunIdx]) : NaN;
        const nilaiProvVal = nilaiProvIdx !== -1 ? parseNumberVal(row[nilaiProvIdx]) : 0;
        const nilaiKabVal = nilaiKabIdx !== -1 ? parseNumberVal(row[nilaiKabIdx]) : 0;
        if (isNaN(tahunVal)) continue;
        dataRows.push({
            provinsi: provVal,
            kabupaten: kabVal,
            sektor: sektorVal,
            tahun: tahunVal,
            nilaiProv: nilaiProvVal,
            nilaiKab: nilaiKabVal
        });
    }
    
    const years = [...new Set(dataRows.map(r => r.tahun))].filter(y => !isNaN(y)).sort((a, b) => a - b);
    console.log("Years found:", years);
    
    const tahunAwal = years[0];
    const tahunAkhir = years[years.length - 1];
    
    const totalKab = {};
    const totalProv = {};
    dataRows.forEach(r => {
        const kabKey = `${r.kabupaten}_${r.tahun}`;
        const provKey = `${r.provinsi}_${r.tahun}`;
        if (!totalKab[kabKey]) totalKab[kabKey] = 0;
        totalKab[kabKey] += r.nilaiKab;
        if (!totalProv[provKey]) totalProv[provKey] = 0;
        totalProv[provKey] += r.nilaiProv;
    });
    
    const combined = {};
    dataRows.forEach(r => {
        const groupKey = `${r.provinsi}_${r.kabupaten}_${r.sektor}`;
        if (!combined[groupKey]) {
            combined[groupKey] = {
                'Provinsi': r.provinsi || 'Sumatera Utara',
                'Kabupaten/Kota': r.kabupaten || '-',
                'Sektor': r.sektor,
                'Tahun Awal': tahunAwal,
                'Tahun Akhir': tahunAkhir,
                'PDRB Sektor Analisis Awal': 0,
                'PDRB Sektor Analisis Akhir': 0,
                'Total PDRB Analisis Awal': totalKab[`${r.kabupaten}_${tahunAwal}`] || 0,
                'Total PDRB Analisis Akhir': totalKab[`${r.kabupaten}_${tahunAkhir}`] || 0,
                'PDRB Sektor Pembanding Awal': 0,
                'PDRB Sektor Pembanding Akhir': 0,
                'Total PDRB Pembanding Awal': totalProv[`${r.provinsi}_${tahunAwal}`] || 0,
                'Total PDRB Pembanding Akhir': totalProv[`${r.provinsi}_${tahunAkhir}`] || 0
            };
        }
        if (r.tahun === tahunAwal) {
            combined[groupKey]['PDRB Sektor Analisis Awal'] = r.nilaiKab;
            combined[groupKey]['PDRB Sektor Pembanding Awal'] = r.nilaiProv;
        } else if (r.tahun === tahunAkhir) {
            combined[groupKey]['PDRB Sektor Analisis Akhir'] = r.nilaiKab;
            combined[groupKey]['PDRB Sektor Pembanding Akhir'] = r.nilaiProv;
        }
    });
    
    const jsonData = Object.values(combined);
    console.log("Parsed rows count:", jsonData.length);
    fs.writeFileSync('scratch/parsed_payload.json', JSON.stringify(jsonData, null, 2));
    console.log("Saved payload to scratch/parsed_payload.json");
} catch (e) {
    console.error("Error:", e);
}
