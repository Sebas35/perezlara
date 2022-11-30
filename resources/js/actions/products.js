const insurer = document.getElementById ('insurer'), insurance = document.getElementById ('insurance'),
    product = document.getElementById ('product'),
    label_product = document.getElementById ('label-product'),
    group_insurers = document.getElementById ('group-insurers'),
    group_insurances = document.getElementById ('group-insurances'),
    select_content_insurances = document.getElementById ('seguros-select-content'),
    select_content_insurers = document.getElementById ('aseguradoras-select-content'),
    insurances_cards_container = document.getElementById ('insurances-cards-container'),
    insurers_cards_container = document.getElementById ('insurers-cards-container'),
    backdrop = document.getElementById ('backdrop');
let action_product;

insurer.addEventListener ('click', form_insurer);
insurance.addEventListener ('click', form_insurance);
window.addEventListener ('load', index);

function form_insurer(){
    change_form_text ('Nueva aseguradora', 'Registrar aseguradora');
    label_product.textContent = 'Nombre de la aseguradora';
    group_insurers.classList.add ('is-hidden');
    group_insurances.classList.remove ('is-hidden');
    action_product = true;
}

function form_insurance(){
    change_form_text ('Nuevo seguro', 'Registrar seguro');
    label_product.textContent = 'Nombre del seguro';
    group_insurers.classList.remove ('is-hidden');
    group_insurances.classList.add ('is-hidden');
    action_product = false;
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

function insurers(array_insurers){
    insurers_cards_container.innerHTML = null;
    const length = array_insurers.length;
    array_insurers.forEach ((e, index) => {
        const container_card = document.createElement ('div'), card = document.createElement ('div'),
            img_card = document.createElement ('div'), img = document.createElement ('img'),
            container_list = document.createElement ('div'), h3 = document.createElement ('h3'),
            ol = document.createElement ('ol'), insurances = e['seguros'];
        container_card.className = 'container-card';
        card.dataset.id = e['id_aseguradora'];
        card.className = 'card';
        card.style.zIndex = (length - index).toString ();
        img_card.className = 'img-card';
        img.className = 'card-logo';
        img.src = DRIVE.img + e['logo'];
        img_card.appendChild (img);
        container_list.className = 'container-list';
        h3.className = 'title-list';
        h3.appendChild (document.createTextNode ('Seguros'));
        ol.className = 'insurance-list scrollbar';
        if (insurances){
            insurances.split (',').forEach (e => {
                const li = document.createElement ('li');
                li.className = 'insurance';
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
        img.src = DRIVE.img + e['imagen'];
        img_card.append (h3, img);
        insurances_cards_container.appendChild (img_card);
        multiple_option (e['id_seguro'], e['seguro'], select_content_insurances);
    });
}

async function create() {
    await (action_product ? createInsurer () : createInsurance ());
}

async function edit() {
    await (action_product ? editInsurer () : editInsurance ());
}

async function remove() {
    await (action_product ? removeInsurer () : removeInsurance ());
}

async function createInsurer(){
    const form_data = new FormData ();
    form_data.append ('insurer', product.value);
    form_data.append ('FileService', '');
    form_data.append ('collection_insurances', JSON.stringify (elements ('seguros')))
    form_data.append ('logo', file.files[0]);
    const res = await fetch ('aseguradoras/create', {
        method: 'POST',
        body: form_data,
    });
    const data = await res.json ();
    insurers (data['data']);
    alert (data['msg']);
}

async function createInsurance(){
    const form_data = new FormData ();
    form_data.append ('insurance', product.value);
    form_data.append ('FileService', '');
    form_data.append ('collection_insurers', JSON.stringify (elements ('aseguradoras')))
    form_data.append ('icono', file.files[0]);
    const res = await fetch ('seguros/create', {
        method: 'POST',
        body: form_data,
    });
    const data = await res.json ();
    insurances (data['data']);
    alert (data['msg']);
}

async function editInsurer() {
    form_insurer();
    const res = await fetch ('aseguradoras/show', {
        method: 'POST',
        body: new URLSearchParams ({
                                       id_insurer: context_menu.dataset.rel
                                   }),
    });
    const data = await res.json();
    product.value = data['aseguradora'];
    input_active();
    select_active();
}

async function editInsurance(){

}

async function removeInsurer() {

}

async function removeInsurance() {

}