const select_birth_date = document.getElementById ('selected-fecha-nacimiento-select'),
    select_city = document.getElementById ('selected-ciudad-select'),
    select_department = document.getElementById ('selected-departamento-select'),
    address = document.getElementById ('direccion'),
    address2 = document.getElementById ('direccion2'),
    phone2 = document.getElementById ('telefono2');
let filter = false;

function form_text() {
    change_form_text ('Nuevo cliente', 'Registrar cliente');
}

function common_data(){
    const data = givens ();
    data.append ('birthday', select_birth_date.innerHTML);
    data.append ('email', email.value);
    data.append ('phone1', phone.value);
    data.append ('phone2', phone2.value);
    data.append ('address1', address.value);
    data.append ('address2', address2.value);
    data.append ('document_type', document_type.dataset.selected);
    data.append ('city', select_city.dataset.selected);
    return data;
}

async function index() {
    const res = await fetch ('clientes/index', {
        method: 'POST',
        body: new URLSearchParams ({
                                       request: true
                                   }),
    })
    const json = await res.json ();
    table (json);
}

async function create(){
    const res = await fetch ('clientes/create', {
        method: 'POST',
        body: common_data (),
    })
    const data = await res.json ();
    response(data['data'], data['msg'])
}

async function edit() {
    change_form_text ('Editar cliente', 'Actualizar cliente');
    const res = await fetch ('clientes/show', {
        method: 'POST',
        body: new URLSearchParams ({
                                       document: following_words(context_menu.dataset.rel)
                                   }),
    })
    const data = await res.json ();
    names.value = data['Nombres'].trim ();
    surnames.value =  data['Apellidos'].trim ();
    document_number.value = data['documento'].trim ();
    email.value = data['Email'].trim ();
    address.value = data['direccion1'].trim ();
    if(data['direccion2']) {
        address2.value = data['direccion2'].trim ();
    }
    phone.value = data['telefono1'].trim ();
    if (data['telefono2']) {
        phone2.value = data['telefono2'].trim ();
    }
    document_type.dataset.selected = data['id_tipo_documento'];
    document_type.innerHTML = data['descripcion_documento'];
    select_city.dataset.selected = data['id_ciudad'];
    select_city.innerHTML = data['Ciudad'];
    select_department.dataset.selected = data['id_departamento'];
    select_department.innerHTML = data['Departamento'];
    select_birth_date.innerHTML = data['Fecha de nacimiento'];
    input_active ();
    select_active ();
}

async function filter_results(obj_data){
    const res = await fetch ('clientes/filter', {
        method: 'POST',
        body: obj_data,
    })
    const data = await res.json ();
    filter = true;
    table (data);
}

async function search(){
    const res = await fetch ('clientes/search', {
        method: 'POST',
        body: new URLSearchParams ({
                                       search: input_search.value
                                   })
    })
    table (await res.json ())
}

async function update(){
    const form_data = new FormData ();
    form_data.append ('client', JSON.stringify (Object.fromEntries (common_data ())));
    form_data.append ('client_update', following_words (context_menu.dataset.rel));
    const res = await fetch ('clientes/update', {
        method: 'POST',
        body: form_data,
    })
    const data = await res.json ();
    response (data['data'], data['msg'], true);
}

async function remove(){
    const res = await fetch ('clientes/delete', {
        method: 'POST',
        body: new URLSearchParams ({
                                       document: following_words (context_menu.dataset.rel)
                                   }),
    })
    const data = await res.json ();
    table (data['data']);
    alert(data['msg']);
}