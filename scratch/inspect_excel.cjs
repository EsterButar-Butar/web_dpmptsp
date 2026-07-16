const XLSX = require('xlsx');

try {
    const workbook = XLSX.readFile('d:\\Magang\\web_dpmptsp\\test_import.xlsx');
    const firstSheet = workbook.SheetNames[0];
    const worksheet = workbook.Sheets[firstSheet];
    const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
    
    console.log("Total rows:", rows.length);
    console.log("Headers:", rows[0]);
    console.log("Row 1:", rows[1]);
    console.log("Row 2:", rows[2]);
    console.log("Row 3:", rows[3]);
    console.log("Row 4:", rows[4]);
    console.log("Row 5:", rows[5]);
} catch (e) {
    console.error("Error reading file:", e);
}
