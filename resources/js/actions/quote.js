const message_empty_table = document.getElementById ('message-empty-table'),
    table_form = document.getElementById ('table-quote'),
    thead_form = document.getElementById ('thead-quote'),
    titles_quote = document.getElementById('titles-quote'),
    tbody_form = document.getElementById ('tbody-quote'),
    tfoot_form = document.getElementById ('tfoot-quote'),
    document_number = document.getElementById('numero-documento'),
    client = document.getElementById('cliente'),
    label_client = document.getElementById('label-cliente'),
    selected_insurance = document.getElementById('selected-seguro-select'),
    select_content_insurance = document.getElementById ('seguro-select-content'),
    select_content_insurer = document.getElementById ('aseguradora-select-content'),
    select_content_coverages = document.getElementById('coberturas-select-content'),
    filter_content_insurance = document.getElementById ('seguro-filter-content'),
    filter_content_insurer = document.getElementById ('aseguradora-filter-content'),
    insured_value = document.getElementById('valor-asegurado'),
    max_width = Number (window.getComputedStyle (table_form).maxWidth.replace (/[a-zA-Z]+/, '')),
    max_height = Number (window.getComputedStyle (table_form).maxHeight.replace (/[a-zA-Z]+/, ''));

document_number.addEventListener('input',client_name);

async function client_name (e) {
    const form_data = new FormData();
    form_data.append('client_document',e.target.value);
    form_data.append('optional_all','false');
    const res = await fetch('clientes/show',{
       method: 'POST',
       body: form_data,
    });
    const data = await res.json();
    label_client.classList.add('is-label-active');
    client.value = data['Nombres'] + ' ' + data['Apellidos'];
}

function input(tr, colspan = null, value = null) {
    const td = document.createElement ('td'), label = document.createElement ('label'),
        input = document.createElement ('input');
    if(colspan){
        td.colSpan = colspan;
    }
    if(value){
        input.value = value;
    }
    input.className = 'input-title';
    label.className = 'label-title';
    label.appendChild (input);
    td.appendChild (label);
    tr.appendChild (td);
}

function titles(td){
    const price = document.createElement ('th'), deductible = document.createElement ('th');
    price.appendChild (document.createTextNode ('Valor'));
    deductible.appendChild (document.createTextNode ('Deducible'));
    td.append (price, deductible);
}

function add_event() {
    document.querySelectorAll ('.select + .modal .checkbox').forEach (e => e.addEventListener ('input', create_quote_table));
}

function create_quote_table(e) {
    if (e.target.checked) {
        hide_message ();
        titles_quote.classList.remove('is-hidden');
        if (e.target.closest ('#aseguradora-select')){
            thead_form.rows[0].innerHTML +=
                '<th id="aseguradora' + e.target.dataset.id + '" colspan="2">' + e.target.labels.item (0).textContent +
                '</th>';
            if (tbody_form.childElementCount > 0) {
                if (thead_form.rows[0].getElementsByTagName ('th').length === 2) {
                    thead_form.insertRow ();
                }
                titles (thead_form.rows[1]);
                input(tfoot_form.rows[0], 2);
                tfoot_form.classList.remove('is-hidden');
            }
            for (let i = 0; i < tbody_form.childElementCount; i++) {
                input (tbody_form.rows[i]);
                input (tbody_form.rows[i]);
            }
        } else {
            if (thead_form.getElementsByTagName ('th').length > 1 && tbody_form.childElementCount === 0){
                const tr = thead_form.insertRow ();
                for (let i = 0; i < thead_form.rows[0].childElementCount - 1; i++) {
                    titles (tr);
                }
            }
            const tr = tbody_form.insertRow ();
            tr.id = 'cobertura' + e.target.dataset.id;
            tr.insertCell ().appendChild (document.createTextNode (e.target.labels.item (0).innerHTML));
            for (let i = 0; i < thead_form.rows[0].childElementCount - 1; i++) {
                input (tr);
                input (tr);
            }
            if(tfoot_form.rows[0].childElementCount === 1) {
                for (let i = 0; i < thead_form.rows[0].childElementCount - 1; i++) {
                    tfoot_form.classList.remove('is-hidden');
                    input(tfoot_form.rows[0], 2);
                }
            }
        }
        if (table_form.offsetWidth > max_width || table_form.offsetHeight > max_height){
            table_form.classList.add ('is-table-quote-active');
        }
    } else {
        if (e.target.closest ('#coberturas-select')) {
            document.getElementById ('cobertura' + e.target.dataset.id).remove ();
            if (tbody_form.childElementCount === 0) {
                tfoot_form.classList.add('is-hidden');
                Array.from(tfoot_form.rows[0].getElementsByTagName('td')).forEach(e => e.remove())
                thead_form.rows[0].childElementCount > 1 ? thead_form.rows[1].remove () : show_message ();
            }
        } else {
            const remove = Array.from (thead_form.rows[0].children).findIndex (x => x.id === 'aseguradora' + e.target.dataset.id);
            if(thead_form.childElementCount > 1) {
                thead_form.rows[1].removeChild(thead_form.rows[1].children.item(remove * 2 - 1));
                thead_form.rows[1].removeChild(thead_form.rows[1].children.item(remove * 2 - 2));
            }
            thead_form.rows[0].removeChild(thead_form.rows[0].children.item(remove));
            Array.from (tbody_form.children).forEach (e => {
                e.removeChild(e.children.item(remove * 2));
                e.removeChild(e.children.item(remove * 2 - 1));
            });
            if(tfoot_form.rows[0].childElementCount > 1){
                tfoot_form.rows[0].removeChild(tfoot_form.rows[0].children.item(remove));
            }
            if (thead_form.childElementCount === 1){
                if (thead_form.rows[0].childElementCount === 1){
                    show_message ();
                }
            } else if (thead_form.rows[1].childElementCount === 0){
                thead_form.deleteRow(1);
                tfoot_form.classList.add('is-hidden');
            }
        }
        if (thead_form.offsetWidth < max_width || tbody_form.offsetWidth < max_width){
            table_form.classList.remove ('is-table-quote-active');
        }
    }
}

function hide_message(){
    message_empty_table.classList.add ('is-message-empty-table-inactive');
}

function show_message(){
    message_empty_table.classList.remove ('is-message-empty-table-inactive');
    thead_form.rows.item(0).classList.add('is-hidden');
}

function form_text() {
    change_form_text ('Nueva cotización', 'Registrar cotización');
    titles_quote.classList.add('is-hidden');
    reset_table();
    show_message();
    tfoot_form.classList.add('is-hidden');
}

async function index() {
    const res = await fetch ('cotizaciones/data', {
        method: 'POST',
        body: new URLSearchParams ({
                                       request: true,
                                   }),
    })
    const data = await res.json ();
    data['insurances'].forEach(e => {
        one_option (e['id_seguro'], e['seguro'], select_content_insurance);
        multiple_option (e['id_seguro'], e['seguro'], filter_content_insurance);
    })
    data['insurers'].forEach(e => {
        multiple_option (e['id_aseguradora'], e['aseguradora'], select_content_insurer);
        multiple_option (e['id_aseguradora'], e['aseguradora'], filter_content_insurer);
    })
    data['coverages'].forEach(e => {
        multiple_option (e['id_cobertura'], e['cobertura'], select_content_coverages);
    })
    select_multiple(select_content_insurer);
    select_multiple(select_content_coverages);
    add_event();
    table(data['quotes']);
}

function comparison_chart_data() {
    const quote = {}, insurers = elements('aseguradora'), coverages = elements('coberturas'),
        premiums_values = Array.from(tfoot_form.rows[0].getElementsByTagName('input'));
    insurers.forEach((i,index) => {
        const option = {
            insurer: i,
            premium_value: premiums_values[index].value,
            coverages: {}
        };
        coverages.forEach((c,index2) => {
            option['coverages']['coverage' + index2] = {
                id: c,
                price: tbody_form.rows[index2].children.item(index * 2 + 1).querySelector('.input-title').value,
            }
        });
        quote['option' + index] = option;
    });
    return quote;
}

async function create() {
    const form_data = new FormData();
    form_data.append('Create', JSON.stringify({
        client: document_number.value,
        insurance: selected_insurance.dataset.selected,
        insured_value: insured_value.value,
        user: 0,
        data_quote: comparison_chart_data(),
    }));
    const res = await fetch ('cotizaciones/create', {
        method: 'POST',
        body: form_data
    })
    const data = await res.json ();
    response (data['data'], data['msg']);
}

function reset_table() {
    document.querySelectorAll('#thead-quote tr:first-child th:not(th:first-child)').forEach( e => e.remove());
    const tr_titles = document.querySelector('#thead-quote tr:nth-child(2)');
    if(tr_titles) {
        tr_titles.remove();
    }
    tbody_form.innerHTML = null;
    document.querySelectorAll('#tfoot-quote tr td').forEach( e => e.remove());
}

async function edit() {
    change_form_text ('Editar cotización', 'Actualizar cotización')
    const res = await fetch ('cotizaciones/show', {
        method: 'POST',
        body: new URLSearchParams ({
                                       IdQuote: context_menu.dataset.rel
                                   }),
    })
    const data = await res.json ();
    const insurers = data['id_aseguradora'].split(','), names_insurers = data['nombre_aseguradora'].split(','),
        ids_coverages = data['id_cobertura'].split(','), names_coverages = data['nombre_cobertura'].split(','),
        price = data['Precio'].split(','), deductible = data['Deducible'].split(','), premium_value = data['Valor prima'].split(',');
    document_number.value = data['Documento'];
    client.value = data['Cliente'];
    selected_insurance.textContent = data['Seguro'];
    selected_insurance.dataset.selected = data['id_seguro'];
    insured_value.value = data['Valor asegurado'];
    titles_quote.classList.remove('is-hidden');
    reset_table();
    table_form.classList.add ('is-table-quote-active');
    hide_message();
    thead_form.insertRow ();
    insurers.forEach( (e, index) => {
        document.getElementById('aseguradora-select' + e).checked = true;
        thead_form.rows[0].innerHTML +=
            '<th id="aseguradora' + e + '" colspan="2">' + names_insurers[index] +
            '</th>';
        titles (thead_form.rows[1]);
        input(tfoot_form.rows[0], 2, premium_value[index]);
        tfoot_form.classList.remove('is-hidden');
    });
    ids_coverages.forEach( (e, index) => {
        document.getElementById('coberturas-select' + e).checked = true;
        const tr = tbody_form.insertRow ();
        tr.id = 'cobertura' + e;
        tr.insertCell ().appendChild (document.createTextNode(names_coverages[index]));
        let c = index;
        for (let i = 0; i < insurers.length; i++) {
            input (tbody_form.rows[index], null, price[c]);
            input (tbody_form.rows[index], null, deductible[c]);
            c += i + ids_coverages.length;
        }
    });
    input_active();
    select_active();
}

async function filter_results(obj_data) {
    const res = await fetch ('cotizaciones/filter', {
        method: 'POST',
        body: obj_data,
    })
    const data = await res.json ();
    filter = true;
    table (data);
}

async function update() {
    const form_data = new FormData();
    form_data.append('Update',JSON.stringify({
        id: context_menu.dataset.rel,
        client: document_number.value,
        insurance: selected_insurance.dataset.selected,
        insured_value: insured_value.value,
        data_quote: comparison_chart_data(),
    }))
    const res = await fetch ('cotizaciones/update', {
        method: 'POST',
        body: form_data,
    })
    const data = await res.json ();
    response (data['data'],data['msg'], true);
}

async function remove(){
    const res = await fetch ('cotizaciones/delete', {
        method: 'POST',
        body: new URLSearchParams({
            id_quote: context_menu.dataset.rel
        }),
    })
    const data = await res.json ();
    table (data['data']);
    console.log(data['msg']); // Mostrar alerta de eliminación exitosa
}