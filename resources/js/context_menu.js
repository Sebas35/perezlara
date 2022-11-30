const context_menu = document.getElementById ('context-menu'),
    btn_edit = document.getElementById ('edit'),
    true_confirm = document.getElementById ('true-confirm');

document.body.addEventListener ('click', close_context_menu);
btn_edit.addEventListener ('click', () => {
    action = false;
    edit ();
});
true_confirm.addEventListener ('click', remove);

/* Abrir menú contextual*/
function open_context_menu(e){
    e.preventDefault ();
    context_menu.classList.add ('is-context-menu-active');
    context_menu.style.top = e.clientY + context_menu.offsetHeight > window.innerHeight
                             ? window.innerHeight - context_menu.offsetHeight - 1 + 'px'
                             : e.clientY + 'px';
    context_menu.style.left = e.clientX + context_menu.offsetWidth > window.innerWidth
                              ? window.innerWidth - context_menu.offsetWidth + 'px'
                              : e.clientX + 'px';
    const element = e.target.closest('.row');
    if (element) {
        context_menu.dataset.rel = element.firstElementChild.textContent;
    } else {
        action_product = true;
        context_menu.dataset.rel = e.target.closest('.card').dataset.id;
    }
}

/* Cerrar menú contextual*/
function close_context_menu(){
    if (document.querySelector ('.is-context-menu-active')) {
        context_menu.classList.remove ('is-context-menu-active');
    }
}