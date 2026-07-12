import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const viewsDir = path.join(__dirname, '..', 'resources', 'views');

const dtCss = `<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">`;

const dtInclude = `@include('admin.partials.datatables-head')`;

const scriptBlockRe = /<!-- Include jQuery and DataTables JS -->\s*<script src="https:\/\/code\.jquery\.com\/jquery-3\.7\.0\.min\.js"><\/script>\s*<script src="https:\/\/cdn\.datatables\.net\/1\.13\.6\/js\/jquery\.dataTables\.min\.js"><\/script>\s*<script src="https:\/\/cdn\.datatables\.net\/buttons\/2\.4\.1\/js\/dataTables\.buttons\.min\.js"><\/script>\s*<script src="https:\/\/cdn\.datatables\.net\/buttons\/2\.4\.1\/js\/buttons\.html5\.min\.js"><\/script>\s*<script src="https:\/\/cdn\.datatables\.net\/buttons\/2\.4\.1\/js\/buttons\.print\.min\.js"><\/script>\s*<script src="https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/jszip\/3\.10\.1\/jszip\.min\.js"><\/script>\s*<script src="https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/pdfmake\/0\.2\.7\/pdfmake\.min\.js"><\/script>\s*<script src="https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/pdfmake\/0\.2\.7\/vfs_fonts\.js"><\/script>/g;

const scriptInclude = `@include('admin.partials.datatables-scripts')`;

function walk(dir, files = []) {
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) walk(full, files);
    else if (entry.name.endsWith('.blade.php') && !full.includes('partials\\datatables') && !full.includes('partials/datatables')) files.push(full);
  }
  return files;
}

let count = 0;
for (const file of walk(viewsDir)) {
  let content = fs.readFileSync(file, 'utf8');
  const orig = content;

  if (content.includes(dtCss)) content = content.replace(dtCss, dtInclude);
  content = content.replace(scriptBlockRe, scriptInclude);

  content = content.replace(
    /\$\('#(\w+)'\)\.DataTable\(\{\s*dom:\s*'Bfrtip',\s*buttons:\s*\[\s*'copy',\s*'csv',\s*'excel',\s*'pdf',\s*'print'\s*\]\s*\}\);/g,
    "initCrmDataTable('#$1');"
  );
  content = content.replace(
    /\$\('#(\w+)'\)\.DataTable\(\{\s*dom:\s*'Bfrtip',\s*buttons:\s*\['copy',\s*'csv',\s*'excel',\s*'pdf',\s*'print'\],\s*order:\s*(\[\[[^\]]+\]\])\s*(?:\/\/[^\n]*)?\s*\}\);/g,
    "initCrmDataTable('#$1', { order: $2 });"
  );
  content = content.replace(
    /\$\('#(\w+)'\)\.DataTable\(\{\s*dom:\s*'Bfrtip',\s*buttons:\s*\[\s*'copy',\s*'csv',\s*'excel',\s*'pdf',\s*'print'\s*\],\s*pageLength:\s*(\d+),\s*order:\s*(\[\[[^\]]+\]\]),\s*responsive:\s*true\s*\}\);/g,
    "initCrmDataTable('#$1', { pageLength: $2, order: $3 });"
  );

  content = content.replace(/container-xxl flex-grow-1 container-p-y"/g, 'container-xxl flex-grow-1 container-p-y crm-page"');
  content = content.replace(/container-p-y crm-page crm-page"/g, 'container-p-y crm-page"');
  content = content.replace(/class="table table-bordered"/g, 'class="table crm-table"');
  content = content.replace(/id="leadsTable" class="table crm-table"/g, 'id="leadsTable" class="table crm-table"');

  if (content !== orig) {
    fs.writeFileSync(file, content);
    count++;
    console.log('Updated:', path.relative(viewsDir, file));
  }
}
console.log('Total:', count);
