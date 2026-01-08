<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Input Transaksi Zefatex (Batch)
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= base_url('zefatex') ?>" class="btn btn-secondary btn-sm me-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <h1 class="h3 mb-0 text-gray-800 fw-bold ml-3">Input Transaksi (Upload Foto)</h1>
</div>

<!-- Upload Section -->
<div class="card shadow mb-4">
    <div class="card-body text-center p-5 border-dashed" style="border: 2px dashed #ccc; border-radius: 10px; background: #f8f9fa;">
        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
        <h4 class="mb-2">Upload Foto Invoice</h4>
        <p class="text-muted mb-4">Format: JPG, PNG.</p>
        <button class="btn btn-primary px-4" onclick="document.getElementById('fileInput').click()">
            Pilih File
        </button>
        <input type="file" id="fileInput" name="images[]" multiple accept="image/*" class="d-none">
    </div>
</div>

<!-- Staging Table Container -->
<div class="card shadow mb-4" id="stagingCard" style="display: none;">
    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Hasil Scan Invoice <span id="countBadge" class="badge bg-info text-white">0</span></h6>
        <button class="btn btn-success shadow-sm" id="btnSaveAll">
            <i class="fas fa-save me-2"></i> Simpan Semua Transaksi
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0 align-middle">
                <thead class="bg-light text-center">
                    <tr>
                        <th width="100">Foto</th>
                        <th width="200">No. Invoice & Tanggal</th>
                        <th width="200">Customer (Bill To)</th>
                        <th>Item Ringkasan</th>
                        <th width="150">Total (IDR)</th>
                        <th width="50">Aksi</th>
                    </tr>
                </thead>
                <tbody id="stagingBody">
                    <!-- Javascript will populate this -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Item Editor Modal -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Item Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="currentEditIndex">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Deskripsi</th>
                            <th width="100">Qty</th>
                            <th width="150">Harga (IDR)</th>
                            <th width="150">Total</th>
                            <th width="50">#</th>
                        </tr>
                    </thead>
                    <tbody id="modalItemsBody"></tbody>
                </table>
                <button class="btn btn-sm btn-outline-primary" onclick="addModalDetailRow()">+ Tambah Baris</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveModalItems()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // GLOBAL STATE
    let draftInvoices = []; // Objects: { id, file, invoice_number, transaction_date, customer_name, items: [], total, status }

    $(document).ready(function() {
        $('#fileInput').change(handleFiles);
        $('#btnSaveAll').click(saveAllTransactions);
    });

    async function handleFiles() {
        const files = this.files;
        if (!files.length) return;

        $('#stagingCard').fadeIn();

        // Process Loop
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const tempId = Date.now() + Math.random().toString(36).substr(2, 9);
            
            // Add placeholder row
            addPlaceholderRow(tempId, file);

            // Process OCR
            try {
                const data = await processOCR(file);
                updateRowData(tempId, data);
            } catch (err) {
                console.error(err);
                markRowError(tempId, "Gagal Scan: " + err.message);
            }
        }
        // Reset input for next batch
        this.value = '';
    }

    function addPlaceholderRow(id, file) {
        const url = URL.createObjectURL(file);
        const row = `
            <tr id="row-${id}" class="align-middle">
                <td class="text-center p-2">
                    <img src="${url}" class="img-thumbnail" style="height: 60px; width: 60px; object-fit: cover; cursor: pointer;" onclick="window.open('${url}','_blank')">
                    <div class="spinner-border text-primary spinner-border-sm mt-2" role="status" id="spinner-${id}"></div>
                </td>
                <td><span class="text-muted fst-italic">Sedang membaca...</span></td>
                <td><span class="text-muted fst-italic">Sedang membaca...</span></td>
                <td><span class="text-muted fst-italic">Sedang membaca...</span></td>
                <td class="fw-bold">-</td>
                <td class="text-center">
                    <button class="btn btn-danger btn-sm" onclick="removeDraft('${id}')"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
        $('#stagingBody').append(row);
        updateCount();
    }

    /* -------------------------------------------------------------------------- */
    /*                                 OCR ENGINE                                 */
    /* -------------------------------------------------------------------------- */
    async function processOCR(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = async (e) => {
                try {
                    // 1. Canvas Pre-process
                    const image = new Image();
                    image.src = e.target.result;
                    image.onload = async function() {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = image.width;
                        canvas.height = image.height;
                        ctx.drawImage(image, 0, 0);

                        // Grayscale & Contrast
                        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const d = imgData.data;
                        for (let i = 0; i < d.length; i += 4) {
                            const avg = (d[i] + d[i + 1] + d[i + 2]) / 3;
                            const color = avg > 128 ? 255 : 0;
                            d[i] = d[i + 1] = d[i + 2] = color;
                        }
                        ctx.putImageData(imgData, 0, 0);

                        // 2. Tesseract
                        const result = await Tesseract.recognize(
                            canvas.toDataURL('image/jpeg'),
                            'eng'
                        );
                        
                        // 3. Parse Rule
                        const parsed = parseInvoiceText(result.data.text);
                        parsed.file = file; // Attach original file
                        resolve(parsed);
                    };
                } catch (er) { reject(er); }
            };
            reader.readAsDataURL(file);
        });
    }

    function parseInvoiceText(text) {
        const lines = text.split('\n');
        let details = {
            invoice_number: '',
            transaction_date: '',
            customer_name: '',
            items: [],
            total: 0
        };

        // --- A. INVOICE NO ---
        // Try strict extraction first
        let invMatch = text.match(/#\s*([0-9]{3,})/i) || text.match(/Invoice\s*[:#.]?\s*([A-Za-z0-9\/-]+)/i);
        if (invMatch) {
            let clean = (invMatch[1] || invMatch[0]).replace(/[^A-Za-z0-9\/-]/g, '');
            details.invoice_number = '#' + clean.replace(/^#/, '');
        } 

        // --- B. DATE & FALLBACK INVOICE NO ---
        let dateMatch = text.match(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/);
        
        if (dateMatch && parseInt(dateMatch[3]) > 2020) {
            let d = parseInt(dateMatch[1]);
            let m = parseInt(dateMatch[2]);
            let y = dateMatch[3];
            
            // Auto-fix US Date format (mm/dd/yyyy) if month > 12
            // e.g. 06/25/2023 -> d=6, m=25 -> Oops
            // If m > 12 but d <= 12, swap them
            if (m > 12 && d <= 12) {
                 let temp = d; d = m; m = temp;
            }
            // Or if user just scanned d/m but logic thought m/d?
            // The regex was (\d \d \d).
            // Let's force check: if m > 12, swap.
            if (m > 12) {
                let temp = d; d = m; m = temp;
            }
            
            details.transaction_date = `${y}-${String(m).padStart(2,'0')}-${String(d).padStart(2,'0')}`;

            // If Invoice No missing, generate from Date
            if (!details.invoice_number) {
                details.invoice_number = `INV/${y}${String(m).padStart(2,'0')}${String(d).padStart(2,'0')}`;
            }
        } else {
            // Default today
            details.transaction_date = new Date().toISOString().split('T')[0];
             if (!details.invoice_number) {
                 // Fallback completely
                 let t = new Date();
                 let y = t.getFullYear(), m = String(t.getMonth()+1).padStart(2,'0'), d = String(t.getDate()).padStart(2,'0');
                 details.invoice_number = `INV/${y}${m}${d}/AUTO`;
             }
        }

        // --- C. CUSTOMER (BILL TO) ---
        // Requirement: "ambil bill to lengkap sama simbol kurungnya"
        // Pattern: Line explicitly containing "Bill To" then capture text.
        // OR: Line containing "Name ( ... )".
        
        let foundCust = false;
        
        // Strategy 1: Look for patterns like "Name ( Bracket Info )"
        // This is very specific to the user's request.
        // We filter out common headers to avoid false positives.
        const headerWords = ['Zefatex', 'Invoice', 'Bill To', 'From', 'Date', 'Page', 'Total'];
        
        for (let line of lines) {
            if (line.includes('(') && line.includes(')') && line.length > 5) {
                // Potential target. Remove "Zefatex" or "From" if present
                let clean = line.replace(/Zefatex|From|Bill To/gi, '').trim();
                // Check if it still looks valid (has letters)
                if (/[A-Za-z]/.test(clean) && clean.length > 3) {
                    details.customer_name = clean;
                    foundCust = true;
                    break;
                }
            }
        }

        // Strategy 2: If no bracket name, check 'Bill To' neighbors
        if (!foundCust) {
             let idx = lines.findIndex(l => l.toLowerCase().includes('bill to'));
             if (idx !== -1) {
                 // Check same line
                 let sameLine = lines[idx].replace(/Bill To[:.]?/gi, '').trim();
                 if (sameLine.length > 3 && !headerWords.includes(sameLine)) {
                     details.customer_name = sameLine;
                 } else if (idx + 1 < lines.length) {
                     // Check next line
                     let nextLine = lines[idx+1].trim();
                     if (nextLine.length > 2 && !headerWords.includes(nextLine)) {
                          details.customer_name = nextLine;
                     }
                 }
             }
        }

        if (!details.customer_name) details.customer_name = "Cash Customer"; // Fallback

        // --- D. ITEMS ---
        // Require: Qty (dots/commas), Description (inc brackets), Price, Amount
        // Regex for Price line: " ... Rp 50.000 ..."
        
        for (let line of lines) {
            line = line.trim();
            if(!line.includes('Rp')) continue;
            if(/Total|Subtotal|Tax/i.test(line)) continue;

            // Extract Money parts
            let moneys = line.match(/Rp[\d,.]+/g);
            if (!moneys) continue;
            
            // Assume 2 moneys = Unit Price & Total Amount
            // Assume 1 money = Unit Price or Total
            
            let priceRaw = moneys[0];
            let priceVal = parseFloat(priceRaw.replace(/Rp/g, '').replace(/\./g, '').replace(',', '.')); // IDR Format
            
            // Remove moneys from line to parse Qty & Desc
            let rest = line;
            moneys.forEach(m => rest = rest.replace(m, ''));
            rest = rest.trim();
            
            // Parse Qty: "100.5" or "10" at end of string usually? 
            // OR "10 x 50.000"?
            // Tesseract often outputs: "Desc 10 Rp50000"
            
            // Find last number in the remaining string
            let qtyMatch = rest.match(/(\d+[\.,]?\d*)\s*$/);
            let qtyVal = 1;
            let desc = rest;
            
            if (qtyMatch) {
                // "100.5" or "100,5" -> User wants exact extraction logic, but DB is float.
                // If IDR locale, dot=thousand, comma=decimal.
                // But Qty often follows English (dot=decimal) in tech generated invoices.
                // Context: 10075.91 (Dot used).
                let qStr = qtyMatch[1];
                if (qStr.includes('.')) {
                    qtyVal = parseFloat(qStr); // 10.5 -> 10.5
                } else {
                    qtyVal = parseFloat(qStr.replace(',', '.'));
                }
                
                desc = rest.substring(0, qtyMatch.index).trim();
            }
            
            if (desc.length > 0) {
                // Final Check: Desc shouldn't be too short
                details.items.push({
                    desc: desc, // Full text including brackets
                    qty: qtyVal,
                    price: priceVal,
                    amount: qtyVal * priceVal
                });
            }
        }
        
        if (details.items.length === 0) {
            // Add dummy
            details.items.push({desc: "Item Manual", qty: 1, price: 0, amount: 0});
        }
        
        // Sum internal total
        details.total = details.items.reduce((sum, item) => sum + item.amount, 0);

        return details;
    }

    /* -------------------------------------------------------------------------- */
    /*                               UI UPDATES                                   */
    /* -------------------------------------------------------------------------- */

    function updateRowData(id, data) {
        $(`#spinner-${id}`).remove();
        
        // Store in global
        draftInvoices.push({ id: id, ...data });

        // Build HTML Inputs
        const row = $(`#row-${id}`);
        row.html(`
             <td class="text-center p-2 position-relative">
                <img src="${URL.createObjectURL(data.file)}" class="img-thumbnail" style="height: 60px; width: 60px; object-fit: cover;">
                <button class="btn btn-sm btn-light position-absolute bottom-0 start-50 translate-middle-x py-0" style="font-size:10px" onclick="viewImage('${id}')">Zoom</button>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm mb-1 fw-bold main-inv" value="${data.invoice_number}" placeholder="No. Invoice" onchange="updateDraftMeta('${id}', 'invoice_number', this.value)">
                <input type="date" class="form-control form-control-sm text-muted" value="${data.transaction_date}" onchange="updateDraftMeta('${id}', 'transaction_date', this.value)">
            </td>
            <td>
                <textarea class="form-control form-control-sm" rows="2" placeholder="Nama Customer" onchange="updateDraftMeta('${id}', 'customer_name', this.value)">${data.customer_name}</textarea>
            </td>
            <td>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge bg-secondary item-count">${data.items.length} Item</span>
                    <button class="btn btn-outline-primary btn-sm py-0" onclick="editItems('${id}')">Edit Item</button>
                </div>
                <small class="text-muted fst-italic item-preview">${data.items[0].desc.substring(0, 30)}...</small>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm fw-bold text-success total-display" value="Rp ${data.total.toLocaleString('id-ID')}" readonly>
            </td>
            <td class="text-center">
                <button class="btn btn-danger btn-sm" onclick="removeDraft('${id}')"><i class="fas fa-trash"></i></button>
                <div class="status-icon mt-2"></div>
            </td>
        `);
    }

    function removeDraft(id) {
        draftInvoices = draftInvoices.filter(d => d.id !== id);
        $(`#row-${id}`).remove();
        updateCount();
        if (draftInvoices.length === 0) $('#stagingCard').fadeOut();
    }

    function updateCount() {
        $('#countBadge').text($('tr[id^="row-"]').length);
    }

    function updateDraftMeta(id, field, value) {
        let draft = draftInvoices.find(d => d.id === id);
        if(draft) draft[field] = value;
    }

    /* -------------------------------------------------------------------------- */
    /*                             ITEM MODAL Logic                               */
    /* -------------------------------------------------------------------------- */
    function editItems(id) {
        const draft = draftInvoices.find(d => d.id === id);
        if(!draft) return;

        $('#currentEditIndex').val(id);
        $('#modalItemsBody').empty();

        draft.items.forEach((item, idx) => {
            addModalRowHTML(item.desc, item.qty, item.price);
        });

        // Add extra line if empty
        if(draft.items.length === 0) addModalDetailRow();

        $('#itemModal').modal('show');
    }

    function addModalDetailRow() {
        addModalRowHTML('', 1, 0);
    }

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }

    function addModalRowHTML(desc, qty, price) {
        let amount = qty * price;
        // Format Initial Price
        let priceStr = price.toString(); 
        // If price comes in as 50000, we want Rp 50.000
        // But if it's 0, just "Rp 0" or empty? Let's use standard formatting
        let priceFormatted = formatRupiah(price.toFixed(0), 'Rp');

        let row = `
            <tr>
                <td><input type="text" class="form-control form-control-sm m-desc" value="${desc}"></td>
                <td><input type="number" class="form-control form-control-sm m-qty" value="${qty}" step="0.01" oninput="calcModalRow(this)"></td>
                <td><input type="text" class="form-control form-control-sm m-price" value="${priceFormatted}" onkeyup="formatPriceInput(this)" oninput="calcModalRow(this)"></td>
                <td><input type="text" class="form-control form-control-sm m-amount bg-light" value="${amount.toLocaleString('id-ID')}" readonly></td>
                <td><button class="btn btn-danger btn-sm py-0" onclick="$(this).closest('tr').remove()">&times;</button></td>
            </tr>
        `;
        $('#modalItemsBody').append(row);
    }

    // New Helper for onkeyup
    function formatPriceInput(input) {
        input.value = formatRupiah(input.value, 'Rp');
    }

    function calcModalRow(input) {
        let tr = $(input).closest('tr');
        let qty = parseFloat(tr.find('.m-qty').val()) || 0;
        
        // Parse Price: Remove non-digits
        let priceRaw = tr.find('.m-price').val();
        let price = parseInt(priceRaw.replace(/[^0-9]/g, '')) || 0;
        
        tr.find('.m-amount').val("Rp " + (qty*price).toLocaleString('id-ID'));
    }

    function saveModalItems() {
        let id = $('#currentEditIndex').val();
        let draft = draftInvoices.find(d => d.id === id);
        
        let newItems = [];
        let total = 0;
        
        $('#modalItemsBody tr').each(function() {
            let desc = $(this).find('.m-desc').val();
            let qty = parseFloat($(this).find('.m-qty').val()) || 0;
            
            // Parse Price
            let priceRaw = $(this).find('.m-price').val();
            let price = parseInt(priceRaw.replace(/[^0-9]/g, '')) || 0;

            if(desc) {
                newItems.push({desc, qty, price, amount: qty*price});
                total += (qty*price);
            }
        });

        draft.items = newItems;
        draft.total = total;

        // Update UI Row
        let row = $(`#row-${id}`);
        row.find('.item-count').text(newItems.length + ' Item');
        row.find('.item-preview').text(newItems.length > 0 ? newItems[0].desc.substring(0,25)+'...' : '-');
        row.find('.total-display').val("Rp " + total.toLocaleString('id-ID'));

        $('#itemModal').modal('hide');
    }

    function viewImage(id) {
         let draft = draftInvoices.find(d => d.id === id);
         if(draft) window.open(URL.createObjectURL(draft.file), '_blank');
    }

    /* -------------------------------------------------------------------------- */
    /*                                SAVE LOGIC                                  */
    /* -------------------------------------------------------------------------- */
    async function saveAllTransactions() {
        if (draftInvoices.length === 0) return;

        Swal.fire({
            title: 'Menyimpan Transaksi...',
            html: 'Mohon jangan tutup halaman ini.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        let successCount = 0;
        let failCount = 0;
        
        // Get CSRF Token
        let csrfName = '<?= csrf_token() ?>';
        let csrfHash = '<?= csrf_hash() ?>';

        for (const draft of draftInvoices) {
            // Update UI status
            $(`#row-${draft.id} .status-icon`).html('<i class="fas fa-spinner fa-spin text-primary"></i>');

            try {
                let formData = new FormData();
                formData.append('invoice_number', draft.invoice_number);
                formData.append('customer_name', draft.customer_name);
                formData.append('transaction_date', draft.transaction_date);
                formData.append('image', draft.file); // The File Object
                formData.append('note', 'Batch Upload');
                
                // Append CSRF
                formData.append(csrfName, csrfHash);

                // Items
                draft.items.forEach((item, i) => {
                    formData.append(`item_desc[${i}]`, item.desc);
                    formData.append(`item_qty[${i}]`, item.qty);
                    formData.append(`item_price[${i}]`, item.price);
                });

                // AJAX Post
                let response = await fetch('<?= base_url('zefatex/store') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                let result;
                try {
                     result = await response.json();
                } catch (e) {
                     // If JSON parse fails (e.g. HTML error page), capture text
                     throw new Error("Server Error: " + response.statusText);
                }

                if (response.ok && result.status === 'success') {
                    successCount++;
                    $(`#row-${draft.id} .status-icon`).html('<i class="fas fa-check-circle text-success fa-lg"></i>');
                    $(`#row-${draft.id}`).addClass('table-success');
                    
                    // Update CSRF for next request if rotated (CI4 rotates by default)
                    // Note: In typical CI4 config, rotation might be on. 
                    // Ideally we should get new token from response, but if we process sequentially it might fail if rotated.
                    // For batch, it's safer to rely on session or disable rotation for this route.
                    // Assuming no rotation or handling it:
                    if(result.token) {
                         csrfHash = result.token;
                    }
                    
                } else {
                    let msg = result.message || JSON.stringify(result.errors) || "Unknown Error";
                    throw new Error(msg);
                }

            } catch (err) {
                failCount++;
                console.error("Save Error:", err);
                $(`#row-${draft.id} .status-icon`).html('<i class="fas fa-times-circle text-danger fa-lg" title="'+err.message+'"></i>');
                $(`#row-${draft.id}`).addClass('table-danger');
                $(`#row-${draft.id}`).after(`<tr><td colspan="6" class="text-danger small">Error: ${err.message}</td></tr>`);
            }
        }

        Swal.fire({
            icon: successCount > 0 ? 'success' : 'error',
            title: 'Selesai',
            text: `Berhasil disimpan: ${successCount}. Gagal: ${failCount}.`,
        }).then(() => {
            if(failCount === 0 && successCount > 0) {
                 window.location.href = '<?= base_url('zefatex') ?>';
            }
        });
    }

</script>
<?= $this->endSection() ?>
