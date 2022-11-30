const rol = document.getElementById('selected-rol-select'), password = document.getElementById('password');

function form_text() {
    change_form_text ('Nuevo usuario', 'Registrar usuario');
}

function common_data() {
    const data = givens();
    data.append('email', email.value);
    data.append('phone', phone.value);
    data.append('password', password.value);
    data.append('profile_picture','resources/icons/buttons/profile.svg');
    data.append('document_type',document_type.dataset.selected);
    data.append('role',rol.dataset.selected);
    data.append('state','1');
    return data;
}

async function index() {
    const res = await fetch('usuarios/index',{
        method: 'POST',
        body: new URLSearchParams ({
                                       request: true
                                   }),
    });
    const data = await res.json();
    table(data);
}

async function create () {
    const res = await fetch('usuarios/createWithPermissions',{
        method: 'POST',
        body: common_data(),
    });
    const data = await res.json();
    response (data['data'], data['msg']);
}

async function edit() {
    change_form_text ('Editar usuario', 'Actualizar usuario');
    const res = await fetch ('usuarios/show', {
        method: 'POST',
        body: new URLSearchParams ({
                                       document: following_words(context_menu.dataset.rel)
                                   }),
    });
    const data = await res.json ();
    names.value = data['Nombres'];
    surnames.value = data['Apellidos'];
    document_number.value = data['documento'].trim ();
    document_type.dataset.selected = data['id_tipo_documento'];
    document_type.innerHTML = data['descripcion_documento'];
    email.value = data['Email'].trim ();
    phone.value = data['Celular'].trim ();
    rol.dataset.selected = data['id_rol'];
    rol.innerHTML = data['Rol'];
    input_active();
    select_active();
}

async function update() {
    const form_data = new FormData ();
    form_data.append ('user', JSON.stringify (Object.fromEntries (common_data ())));
    form_data.append ('user_update', following_words (context_menu.dataset.rel));
    form_data.append ('all', 'true');
    const res = await fetch ('usuarios/update', {
        method: 'POST',
        body: form_data,
    });
    const data = await res.json ();
    response (data['data'], data['msg'], true);
}

async function remove(e) {
    const res = await fetch ('usuarios/delete', {
        method: 'POST',
        body: new URLSearchParams ({
                                       document: following_words (context_menu.dataset.rel)
                                   }),
    })
    const data = await res.json ();
    table (data['data']);
    alert(data['msg']);
}