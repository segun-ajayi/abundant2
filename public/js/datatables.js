/**
 * --------------------------------------------------------------------------
 * CoreUI Pro Boostrap Admin Template (2.1.10): datatables.js
 * Licensed under MIT (https://coreui.io/license)
 * --------------------------------------------------------------------------
 */
$('.datatable').DataTable({
    dom: '<"html5buttons"B>lTfgitp',
    buttons: [
        // { extend: 'copy'},
        // {extend: 'csv'},
        {extend: 'excel',
            text: '<span class="fa fa-download"></span> Excel',
            filename: 'montlyReport',
            footer: true,
            title: 'Monthly Report'},
        // {extend: 'pdf', title: 'Monthly Report'},
    ]
});
$('.datatable').attr('style', 'border-collapse: collapse !important');
//# sourceMappingURL=datatables.js.map
// $('#btns')
//     .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );
