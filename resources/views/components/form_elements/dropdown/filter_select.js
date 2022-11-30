function checkbox(id, element, checkbox = '', data_id = null, checked = false) {
    const label = document.createElement ('label'), input = document.createElement ('input');
    input.id = id;
    input.className = 'checkbox';
    input.type = 'checkbox';
    if (data_id) {
        input.dataset.id = data_id;
    }
    label.className = 'label-checkbox';
    label.htmlFor = id;
    label.appendChild (document.createTextNode (checkbox));
    input.checked = checked;
    element.append (input, label);
}

function one_option(id, option, element){
    const li = document.createElement ('li');
    li.role = 'option';
    li.className = 'option';
    li.dataset.id = id;
    li.appendChild (document.createTextNode (option));
    li.addEventListener ('click', choose);
    element.appendChild (li);
}

function multiple_option(id, option, element){
    const li = document.createElement ('li');
    li.role = 'option';
    checkbox ( element.closest('.modal').id + id, li, option, id);
    element.appendChild (li);
}

function select_multiple(element) {
    document.querySelector('[data-target=' + element.closest('.modal').id + ']').dataset.multiple = 'true';
}

function elements(select) {
    return Array.from(document.querySelectorAll('#' + select + '-select .checkbox:checked')).map(e => e.getAttribute('data-id'))
}