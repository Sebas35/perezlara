const code = document.getElementById ('codigo'),
    client = document.getElementById ('cliente'),
    insurance = document.getElementById ('seguro'),
    insurer = document.getElementById ('aseguradora'),
    title = document.getElementById ('titulo-siniestro'),
    sinister_date = document.getElementById ('selected-fecha-select'),
    amount = document.getElementById ('monto'),
    checkbox_monto = document.getElementById ('checkbox-monto'),
    description = document.getElementById ('description'),
    fieldset_files = document.getElementById ('fieldset-files'),
    file = document.getElementById ('file'),
    file_list = document.getElementById ('file-list'),
    filter_content_insurance = document.getElementById ('seguro-filter-content'),
    filter_content_insurer = document.getElementById ('aseguradora-filter-content');

code.addEventListener ('input', show_data_policy);
checkbox_monto.addEventListener ('input', choose_amount);
file.addEventListener ('input', show_files);

function show_files(e){
    files (e.target.files);
}

function files(files, ids = null){
    fieldset_files.classList.remove ('is-hidden');
    Array.from (files).forEach ((e, index) => {
        const li = document.createElement ('li');
        li.className = 'file-element';
        const id = 'file' + file_list.childElementCount;
        checkbox (id, li, e.name ?? e, ids ? ids[index] : ids, true);
        file_list.appendChild (li);
        if (!ids) {
            document.getElementById (id).addEventListener ('input', remove_file);
        }
    });
}

function remove_file(e){
    document.getElementById (e.target.id).parentElement.remove ();
}

async function show_data_policy(){
    const data = await fetch ('polizas/show', {
        method: 'POST',
        body: new URLSearchParams ({
                                       code: code.value,
                                       optional_all: false,
                                   })
    });
    const res = await data.json ();
    if (typeof res === 'object'){
        client.value = res['Cliente'];
        insurance.value = res['Seguro'];
        insurer.value = res['nombre_aseguradora'];
        input_active ([client, insurance, insurer]);
    }
}

function choose_amount(e){
    e.target.checked ? amount.disabled = true : amount.disabled = false;
}

function form_text(){
    change_form_text ('Nuevo siniestro', 'Registrar siniestro');
    clean_checkbox ();
}

function clean_checkbox(){
    file_list.innerHTML = null;
    fieldset_files.classList.add ('is-hidden');
}

async function index(){
    const res = await fetch ('siniestros/data', {
        method: 'POST',
        body: new URLSearchParams ({
                                       request: true,
                                   }),
    })
    const data = await res.json ();
    data['insurers'].forEach (e => multiple_option (e['id_aseguradora'], e['aseguradora'], filter_content_insurer));
    data['insurances'].forEach (e => multiple_option (e['id_seguro'], e['seguro'], filter_content_insurance));
    table (data['claims']);
}

function common_data(datum){
    datum.append ('title', title.value);
    datum.append ('date', sinister_date.textContent);
    datum.append ('description', description.value);
    datum.append ('amount', !checkbox_monto.checked ? amount.value : '');
    datum.append ('policy_code', code.value);
    datum.append ('collection_files', '');
    return datum;
}

async function create(){
    const form_data = common_data (new FormData ());
    Array.from (file.files).forEach ((e, index) => form_data.append ('file' + index, e));
    const res = await fetch ('siniestros/create', {
        body: form_data,
        method: 'post',
    })
    const data = await res.json ();
    response (data['data'], data['msg']);
    clean_checkbox ();
}

async function edit(){
    change_form_text ('Editar Siniestro', 'Actualizar Siniestro')
    const res = await fetch ('siniestros/show', {
        body: new URLSearchParams ({
                                       id: context_menu.dataset.rel
                                   }),
        method: 'post',
    })
    const data = await res.json ();
    client.value = data['Cliente'];
    title.value = data['Titulo'];
    amount.value = data['Monto'];
    if (data['Monto'] === '-'){
        checkbox_monto.checked = true;
        amount.disabled = true;
    } else {
        checkbox_monto.checked = false;
        amount.disabled = false;
    }
    description.value = data['Descripción'];
    code.value = data['Codigo póliza'];
    insurance.value = data['Seguro'];
    insurer.value = data['nombre_aseguradora'];
    sinister_date.textContent = data['Fecha'];
    file_list.innerHTML = null;
    files (data['nombre_archivo'].split (','), data['id_archivo'].split (','));
    input_active ();
    select_active ();
}

async function update(){
    const form_data = new FormData (), sinister = new FormData ();
    sinister.append ('reference_number', context_menu.dataset.rel);
    common_data (sinister);
    form_data.append ('sinister', JSON.stringify (Object.fromEntries (sinister)));
    form_data.append ('collection_delete_files', JSON.stringify (Array.from (document.querySelectorAll ('#file-list' +
                                                                                                            ' .checkbox:not(:checked)'))
                                                                      .map (e => e.getAttribute ('data-id'))));
    Array.from (file.files).forEach ((e, index) => form_data.append ('file' + index, e));
    const res = await fetch ('siniestros/update', {
        method: 'POST',
        body: form_data,
    })
    const data = await res.json ();
    response (data['data'], data['msg'], true)
}

function filter_results(){

}

async function remove(){
    const res = await fetch ('siniestros/delete', {
        body: new URLSearchParams ({
                                       id: context_menu.dataset.rel
                                   }),
        method: 'POST',
    })
    const data = await res.json ();
    table (data['data']);
    alert (data['msg']);
}
