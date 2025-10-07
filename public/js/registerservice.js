

// document.addEventListener('DOMContentLoaded', function () {
//     const departmentSelect = document.getElementById('department');
//     const shipmentField = document.getElementById('shipmentField');
//     const boxField = document.getElementById('boxField');
//     const ATField = document.getElementById('ATField');
//     const ULField = document.getElementById('ul');
//     const XDKField = document.getElementById('xdk');
//     const supplierField = document.getElementById('supplier');

//     departmentSelect.addEventListener('change', function () {
//         const selected = this.value;

//         if (selected === 'outbound') {
//             console.log('hello world');
            
//             shipmentField.style.display = 'block';
//             ULField.style.display = 'block';
//             boxField.style.display = 'none';
//             ATField.style.display = 'none';
//             supplierField.style.display = 'none';
//         } else if (selected === 'inbound') {
//             shipmentField.style.display = 'none';
//             boxField.style.display = 'block';
//             ATField.style.display = 'none';
//             ULField.style.display = 'none';
//             supplierField.style.display = 'block';
//         } else if (selected === 'promo'){
//             shipmentField.style.display = 'none';
//             boxField.style.display = 'none';
//             ATField.style.display = 'block';
//             ULField.style.display = 'none';
//             supplierField.style.display = 'none';
//         } else if (selected === 'xdk') {
//             shipmentField.style.display = 'none';
//             boxField.style.display = 'none';
//             ATField.style.display = 'none';
//             ULField.style.display = 'none';
//             supplierField.style.display = 'block';
//         } else {
//             shipmentField.style.display = 'none';
//             boxField.style.display = 'none';
//             ATField.style.display = 'none';
//             ULField.style.display = 'none';
//             supplierField.style.display = 'none';
//         }
//     });
// });

document.addEventListener('DOMContentLoaded', function () {
    const departmentSelect = document.getElementById('department');

    const fields = {
        shipmentField: document.getElementById('shipmentField'),
        boxField: document.getElementById('boxField'),
        supplierField: document.getElementById('supplier'),
        ULField: document.getElementById('ul')
    };

    // Rules for each department
    const visibilityRules = {
        Inbound:   ['shipmentField', 'boxField', 'supplierField'],
        Outbound:  ['shipmentField', 'ULField'],
        Default:   [] // what shows if nothing matches
    };

    function toggleFields() {
        const selected = departmentSelect.value;
        const visible = visibilityRules[selected] || visibilityRules.Default;

        // Hide all fields first
        Object.values(fields).forEach(field => field.style.display = 'none');

        // Show only the fields defined for this department
        visible.forEach(id => fields[id].style.display = 'block');
    }

    departmentSelect.addEventListener('change', toggleFields);
    toggleFields(); // run once on page load
});
