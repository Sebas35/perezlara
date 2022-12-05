const thead = document.getElementById ('thead'), tbody = document.getElementById ('tbody');

window.addEventListener ('load', index);

function table(data) {
    thead.innerHTML = null;
    tbody.innerHTML = null;
    if (data.length > 0){
        const keys = Object.keys (data[0]);
        thead.insertRow().classList.add('row');
        keys.filter(e => e !== 'id_archivo').forEach(e => {
            const th = document.createElement('th');
            th.appendChild(document.createTextNode(e));
            thead.rows[0].appendChild(th);
        });
        data.forEach (e => {
            const tr = tbody.insertRow ();
            tr.classList.add ('row');
            for (let j = 0; j < keys.length; j++) {
                if(keys[j] === 'id_archivo') {
                    tr.dataset.href = CLOUD.file + e['id_archivo'];
                } else {
                    const td = tr.insertCell ();
                    if (['Aseguradora','Foto'].includes(keys[j])){
                        td.classList.add ('container-img');
                        e[keys[j]].split (',').forEach (e => {
                            const img = document.createElement ('img');
                            img.className = 'img-table';
                            img.src = CLOUD.insurer + e.toString ();
                            td.appendChild (img);
                        });
                    }  else {
                        td.appendChild (document.createTextNode (e[keys[j]]));
                    }
                }
            }
            tr.addEventListener('dblclick',open_file);
        });
        tbody.insertRow ();
    } else {
        const tr = document.createElement ('tr'), th = document.createElement ('th');
        tr.className = 'row';
        th.className = 'row-not-found';
        th.appendChild (document.createTextNode ('No hay resultados'));
        tr.appendChild (th);
        thead.appendChild(tr);
    }
}

function open_file(e) {
    window.open(e.target.closest('.row').dataset.href);
}
