const file = document.getElementById ('file'),
    filename = document.getElementById('filename'),
    pop_filename = document.getElementById('pop-filename');

file.addEventListener('input', show_file);

function info_file(e) {
    filename.textContent = e.target.files[0].name;
    pop_filename.textContent = e.target.files[0].name;
}