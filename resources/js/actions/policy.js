const code = document.getElementById ('codigo'),
    client_document_number = document.getElementById ('numero-documento'),
    label_client = document.getElementById ('label-cliente'),
    policy_customer_document = document.getElementById ('cliente'),
    quotes_fieldset = document.getElementById ('quotes-fieldset'),
    quotes_info = document.getElementById ('quotes-info'),
    insurance = document.getElementById('seguro'),
    insurer = document.getElementById('aseguradora'),
    insured_value = document.getElementById ('valor-asegurado'),
    premium_value = document.getElementById ('valor-prima'),
    start_date = document.getElementById ('selected-fecha-inicio-select'),
    expiration_date = document.getElementById ('selected-fecha-vencimiento-select'),
    payment_date = document.getElementById ('selected-fecha-pago-select'),
    number_months = document.getElementById ('cantidad-meses'),
    filter_content_insurance = document.getElementById ('seguro-filter-content'),
    filter_content_insurer = document.getElementById ('aseguradora-filter-content');
let option_quote;

client_document_number.addEventListener ('input', show_quotes);

function show_file(e) {
    info_file(e);
}

async function show_quotes() {
    const res = await fetch ('cotizaciones/accordingClient', {
        method: 'POST',
        body: new URLSearchParams ({
                                       ClientDocument: client_document_number.value
                                   }),
    });
    const data = await res.json ();
    if (typeof data === 'object') {
        label_client.classList.add ('is-label-active');
        policy_customer_document.value = data[0]['Cliente'];
        table_quotes(data);
    }
}

function table_quotes(data) {
    quotes_info.innerHTML = null;
    quotes_fieldset.classList.remove ('is-hidden');
    data.forEach (e => {
        const tr = document.createElement ('tr'), first_td = document.createElement ('td'),
            second_td = document.createElement ('td'),
            ids_aseguradora_cotizante = e['id_aseguradora_cotizante'].split(',');
        first_td.appendChild (document.createTextNode (e['Seguro']));
        first_td.className = 'td-body-quotes';
        second_td.className = 'td-body-quotes container-img';
        e['Aseguradora'].split (',').forEach ((e, index) => {
            const img = document.createElement ('img');
            img.dataset.id = ids_aseguradora_cotizante[index];
            img.className = 'img-insurers-quotes';
            img.src = CLOUD.insurer + e.toString ();
            img.role = 'option';
            second_td.appendChild (img);
            img.addEventListener('click', quote_data);
        })
        tr.append (first_td, second_td);
        quotes_info.appendChild (tr);
    });
}

async function quote_data(e) {
    if (option_quote !== e.target.dataset.id) {
        const insurer_selected = document.querySelector('.is-insurer-quote-selected');
        if (insurer_selected) {
            insurer_selected.classList.remove('is-insurer-quote-selected');
            document.querySelector('.is-quote-selected').classList.remove('is-quote-selected');
        }
        e.target.classList.add('is-insurer-quote-selected');
        e.target.closest('tr').classList.add('is-quote-selected');
        option_quote = e.target.dataset.id;
        const res = await fetch('cotizaciones/option',{
            method: 'POST',
            body: new URLSearchParams({
                                          AcceptedOption: e.target.dataset.id
                                      })
        });
        const data = await res.json();
        insurance.value = data['seguro'];
        insurer.value = data['aseguradora'];
        insured_value.value = data['valor_asegurado'];
        premium_value.value = data['valor_prima'];
        input_active([insurance, insurer, insured_value, premium_value]);
    }
}

function form_text() {
    change_form_text ('Nueva Póliza', 'Registrar Póliza');
    quotes_info.innerHTML = null;
    quotes_fieldset.classList.add ('is-hidden');
    filename.textContent = null;
    pop_filename.textContent = null;
    delete filename.dataset.id;
}

async function index() {
    const res = await fetch ('polizas/data', {
        method: 'POST',
        body: new URLSearchParams ({
                                       request: true,
                                   }),
    })
    const data = await res.json ();
    data['insurances'].forEach (e => multiple_option (e['id_seguro'], e['seguro'], filter_content_insurance));
    data['insurers'].forEach (e => multiple_option (e['id_aseguradora'], e['aseguradora'], filter_content_insurer));
    table(data['policies']);
}

function common_data(){
    let data = new FormData ();
    data.append ('code', code.value);
    data.append ('DriveFile', '');
    data.append ('start_date', start_date.textContent);
    data.append ('expiration_date', expiration_date.textContent);
    data.append ('payment_date', payment_date.textContent);
    data.append ('months', number_months.value === '' ? 0 : number_months.value);
    data.append ('AcceptedOption', option_quote);
    return data;
}

async function create(){
    const obj_data = common_data ();
    obj_data.append ('policy_document', file.files[0]);
    const res = await fetch ('polizas/create', {
        method: 'POST',
        body: obj_data,
    })
    const data = await res.json ();
    response (data['data'], data['msg']);
    quotes_fieldset.classList.add ('is-hidden');
    quotes_info.innerHTML = null;
    filename.textContent = null;
    pop_filename.textContent = null;
}

async function edit(){
    change_form_text ('Editar Póliza', 'Actualizar Póliza');
    const res = await fetch ('polizas/show', {
        method: 'POST',
        body: new URLSearchParams ({
                                       code: context_menu.dataset.rel
                                   }),
    })
    const data = await res.json ();
    const policy = data['policy'];
    table_quotes(data['quotes']);
    const option_selected = document.querySelector('.img-insurers-quotes[data-id="'+policy["id_aseguradora_cotizante"]+'"]');
    option_selected.classList.add('is-insurer-quote-selected');
    option_selected.closest('tr').classList.add('is-quote-selected');
    code.value = policy['Codigo póliza'];
    client_document_number.value = policy['Documento'];
    policy_customer_document.value = policy['Cliente'];
    premium_value.value = policy['Valor prima'];
    insured_value.value = policy['Valor asegurado'];
    start_date.textContent = policy['Fecha de inicio'];
    expiration_date.textContent = policy['Fecha de vencimiento'];
    payment_date.textContent = policy['Fecha de pago'];
    insurance.value = policy['Seguro'];
    insurer.value = policy['nombre_aseguradora'];
    number_months.value = policy['Cantidad de meses'];
    filename.textContent = policy['nombre_archivo'];
    filename.dataset.id = policy['id_archivo'];
    pop_filename.textContent = policy['nombre_archivo'];
    input_active ();
    select_active ();
}

async function filter_results(){

}

async function update() {
    const form_data = new FormData (),form_data_common = common_data();
    if (file.files[0]) {
        form_data.append ('policy_document', file.files[0]);
        form_data_common.append ('file_id_update', filename.dataset.id);
    }
    form_data.append ('policy', JSON.stringify (Object.fromEntries (form_data_common)));
    form_data.append ('policy_update', context_menu.dataset.rel);
    const res = await fetch ('polizas/update', {
        method: 'POST',
        body: form_data,
    })
    const data = await res.json ();
    response (data['data'], data['msg'], true);
}

async function remove(){
    const res = await fetch ('polizas/delete', {
        method: 'POST',
        body: new URLSearchParams ({
                                       code: context_menu.dataset.rel
                                   }),
    })
    const data = await res.json ();
    table (data['data']);
    alert (data['msg']);
}


