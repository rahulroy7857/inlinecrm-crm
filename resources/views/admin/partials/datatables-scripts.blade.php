@if (empty($crmDataTablesLoaded))
    @php($crmDataTablesLoaded = true)
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
@endif
<script>
window.CRMDataTableDefaults = {
    dom: "<'crm-dt-toolbar'<'crm-dt-export'B><'crm-dt-search'f>>" +
         "rt" +
         "<'crm-dt-footer'<'crm-dt-length'l><'crm-dt-info'i><'crm-dt-paginate'p>>",
    buttons: [
        { extend: 'copy',  text: '<i class="bx bx-copy"></i> Copy',  className: 'crm-dt-btn', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'csv',   text: '<i class="bx bx-file"></i> CSV',   className: 'crm-dt-btn', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'excel', text: '<i class="bx bx-spreadsheet"></i> Excel', className: 'crm-dt-btn', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'pdf',   text: '<i class="bx bxs-file-pdf"></i> PDF', className: 'crm-dt-btn', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'print', text: '<i class="bx bx-printer"></i> Print', className: 'crm-dt-btn', exportOptions: { columns: ':not(:last-child)' } }
    ],
    paging: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
    pagingType: 'full_numbers',
    stripeClasses: ['odd', 'even'],
    responsive: false,
    autoWidth: false,
    order: [],
    columnDefs: [
        { orderable: false, searchable: false, targets: -1 },
        {
            targets: 0,
            orderable: false,
            render: function (data, type, row, meta) {
                if (type !== 'display') return data;
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        }
    ],
    language: {
        search: '',
        searchPlaceholder: 'Search records...',
        lengthMenu: 'Show _MENU_ entries',
        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
        infoEmpty: 'Showing 0 entries',
        infoFiltered: '(filtered from _MAX_ total entries)',
        paginate: {
            first: 'First',
            last: 'Last',
            previous: 'Prev',
            next: 'Next'
        },
        emptyTable: 'No data available'
    }
};

window.initCrmDataTable = function(selector, options) {
    const $el = $(selector);
    if (!$el.length) return null;
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables is not loaded');
        return null;
    }
    if ($.fn.DataTable.isDataTable(selector)) {
        $el.DataTable().destroy();
    }
    try {
        return $el.DataTable($.extend(true, {}, window.CRMDataTableDefaults, options || {}));
    } catch (error) {
        console.error('DataTable init failed for', selector, error);
        return null;
    }
};

window.adjustCrmDataTable = function(selector) {
    if ($.fn.DataTable.isDataTable(selector)) {
        $(selector).DataTable().columns.adjust().draw(false);
    }
};

if (!window.CRMDataTableAutoInitBound) {
    window.CRMDataTableAutoInitBound = true;
    $(function() {
        $('table.crm-table[id]').each(function() {
            if (!this.id || typeof $.fn.DataTable === 'undefined') return;
            if ($.fn.DataTable.isDataTable(this)) return;
            if ($(this).data('crmDtManual')) return;
            initCrmDataTable('#' + this.id, $(this).data('crmDtOptions') || {});
        });
    });
}
</script>
