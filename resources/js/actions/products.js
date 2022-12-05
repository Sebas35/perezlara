const insurer = document.getElementById ('insurer'), insurance = document.getElementById ('insurance'),
    product = document.getElementById ('product'),
    label_product = document.getElementById ('label-product'),
    icon_product = document.getElementById ('icon-product'),
    group_insurers = document.getElementById ('group-insurers'),
    group_insurances = document.getElementById ('group-insurances'),
    select_content_insurances = document.getElementById ('seguros-select-content'),
    select_content_insurers = document.getElementById ('aseguradoras-select-content'),
    insurances_cards_container = document.getElementById ('insurances-cards-container'),
    insurers_cards_container = document.getElementById ('insurers-cards-container'),
    backdrop = document.getElementById ('backdrop');
let action_product;

window.addEventListener ('load', index);
Array.from (document.querySelectorAll ('#product-select .option'))
     .forEach (e => e.addEventListener ('click', close_modal));
insurer.addEventListener ('click', form_new_insurer);
insurance.addEventListener ('click', form_new_insurance);

function show_file(e){
    info_file (e);
    icon_product.src = URL.createObjectURL (e.target.files[0]);
}

function form_new_insurer(){
    reset_inputs ();
    hidden_icon ();
    change_form_text ('Nueva aseguradora', 'Registrar aseguradora');
    form_insurer ();
}

function form_new_insurance(){
    reset_inputs ();
    hidden_icon ();
    change_form_text ('Nuevo seguro', 'Registrar seguro');
    form_insurance ();
}

function form_insurer(){
    label_product.textContent = 'Nombre de la aseguradora';
    group_insurers.classList.add ('is-hidden');
    group_insurances.classList.remove ('is-hidden');
    action_product = true;
}

function form_insurance(){
    label_product.textContent = 'Nombre del seguro';
    group_insurers.classList.remove ('is-hidden');
    group_insurances.classList.add ('is-hidden');
    action_product = false;
}

function hidden_icon(){
    icon_product.src = '';
    icon_product.classList.add ('is-icon-product-inactive');
}

function clean_checkbox(){
    document.querySelectorAll ('.select + .modal .checkbox:checked').forEach (e => e.checked = false)
}

async function index(){
    const res = await fetch ('usuarios/products', {
        method: 'POST',
        body: new URLSearchParams ({
                                       request: true,
                                   }),
    });
    const data = await res.json ();
    insurances (data['insurances']);
    insurers (data['insurers']);
}

async function create() {
    await (action_product ? create_insurer () : create_insurance ());
}

async function update() {
    await (action_product ? update_insurer () : update_insurance ());
}

async function edit() {
    clean_checkbox ();
    icon_product.classList.remove ('is-icon-product-inactive')
    await (action_product ? edit_insurer () : edit_insurance ());
}

async function remove(){
    await (action_product ? remove_insurer () : remove_insurance ());
}

function insurers(array_insurers){
    insurers_cards_container.innerHTML = null;
    const length = array_insurers.length;
    array_insurers.forEach ((e, index) => {
        const container_card = document.createElement ('div'), card = document.createElement ('div'),
            img_card = document.createElement ('div'), img = document.createElement ('img'),
            container_list = document.createElement ('div'), h3 = document.createElement ('h3'),
            ol = document.createElement ('ol'), states = e['estados'];
        container_card.className = 'container-card';
        card.dataset.id = e['id_aseguradora'];
        card.className = 'card';
        card.style.zIndex = (length - index).toString ();
        img_card.className = 'img-card';
        img.className = 'card-logo';
        img.src = CLOUD.insurer + e['logo'];
        img_card.appendChild (img);
        container_list.className = 'container-list';
        h3.className = 'title-list';
        h3.appendChild (document.createTextNode ('Seguros'));
        ol.className = 'insurance-list scrollbar';
        if (states){
            const states_insurances = states.split (',');
            e['seguros'].split (',').forEach ((e, index) => {
                const li = document.createElement ('li');
                li.className = 'insurance';
                if (Number (states_insurances[index]) === 2){
                    li.className += ' is-insurance-inactive';
                }
                li.appendChild (document.createTextNode (e.toString ()));
                ol.appendChild (li);
            });
        } else {
            img_card.className += ' is-inactive';
        }
        container_list.append (h3, ol);
        card.append (img_card, container_list);
        card.addEventListener ('contextmenu', open_context_menu);
        container_card.appendChild (card);
        insurers_cards_container.appendChild (container_card);
        multiple_option (e['id_aseguradora'], e['aseguradora'], select_content_insurers);
    });
    header.style.zIndex = (length + 1).toString ();
    sidebar.style.zIndex = (length + 1).toString ();
    context_menu.style.zIndex = (length + 1).toString ();
    backdrop.style.zIndex = (length + 2).toString ();
}

function insurances(array_insurances){
    insurances_cards_container.innerHTML = null;
    array_insurances.forEach (e => {
        const img_card = document.createElement ('div'), h3 = document.createElement ('h3'),
            img = document.createElement ('img');
        img_card.className = 'img-card';
        h3.className = 'insurance-name';
        h3.appendChild (document.createTextNode (e['seguro']));
        img.className = 'card-logo';
        img.src = CLOUD.insurance + e['imagen'];
        img_card.append (h3, img);
        insurances_cards_container.appendChild (img_card);
        multiple_option (e['id_seguro'], e['seguro'], select_content_insurances);
    });
}

function new_elements(select){
    return Array.from (document.querySelectorAll ('#' + select + '-select .checkbox:not([data-rel]):checked'))
                .map (e => e.getAttribute
                            ('data-id'))
}

function keep_elements(select){
    return Array.from (document.querySelectorAll ('#' + select + '-select .checkbox[data-rel]:checked'))
                .map (e => e.getAttribute ('data-id'));
}

async function create_insurer() {
    const form_data = new FormData();
    form_data.append ('insurer', product.value);
    form_data.append ('DriveFile', '');
    form_data.append ('collection_insurances', JSON.stringify (elements ('seguros')));
    form_data.append ('logo', file.files[0]);
    const res = await fetch ('aseguradoras/create', {
        method: 'POST',
        body: form_data,
    });
    const data = await res.json ();
    insurers (data['data']);
    alert (data['msg']);
}

async function edit_insurer(){
    change_form_text ('Editar aseguradora', 'Actualizar aseguradora')
    form_insurer ();
    const res = await fetch ('aseguradoras/show', {
        method: 'POST',
        body: new URLSearchParams ({
                                       id_insurer: context_menu.dataset.rel
                                   }),
    });
    const data = await res.json ();
    icon_product.src = CLOUD.insurer + data['logo'];
    product.value = data['aseguradora'];
    data['id_seguros'].split (',').forEach ((e, index) => {
        const option = document.getElementById ('seguros-select' + e),
            id_insurances_insurer = data['id_seguros_aseguradora'].split (',');
        option.checked = true;
        option.dataset.rel = id_insurances_insurer[index];
    });
    input_active ();
}

async function update_insurer() {
    const form_data = new FormData ();
    form_data.append ('id_insurer', context_menu.dataset.rel);
    form_data.append ('insurer', product.value);
    form_data.append ('DriveFile', '');
    form_data.append ('collection_insurances', JSON.stringify ({
                                                                   "collection_create": new_elements('seguros'),
                                                                   "collection_keep": keep_elements('seguros')
                                                               }));
    if (file.files[0]) {
        form_data.append('logo', file.files[0]);
    }
    const res = await fetch ('aseguradoras/update', {
        method: 'POST',
        body: form_data,
    });
    const data = res.json ();
}

async function remove_insurer(){

}

async function create_insurance() {
    const form_data = new FormData();
    form_data.append ('insurance', product.value);
    form_data.append ('DriveFile', '');
    form_data.append ('collection_insurers', JSON.stringify (elements ('aseguradoras')))
    const res = await fetch ('seguros/create', {
        method: 'POST',
        body: form_data,
    });
    const data = await res.json ();
    insurers (data['data']);
    alert (data['msg']);
}

async function edit_insurance(){
    change_form_text ('Editar seguro', 'Actualizar seguro')
    form_insurance ();
}

async function update_insurance() {

}

async function remove_insurance(){

}
