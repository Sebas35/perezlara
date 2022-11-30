const thead = document.getElementById ('thead'), tbody = document.getElementById ('tbody');

window.addEventListener ('load', index);

function table(data) {
    thead.innerHTML = null;
    tbody.innerHTML = null;
    if (data.length > 0){
        const keys = Object.keys (data[0]);
        thead.insertRow().classList.add('row');
        keys.forEach(e => {
            const th = document.createElement('th');
            th.appendChild(document.createTextNode(e));
            thead.rows[0].appendChild(th);
        });
        data.forEach (e => {
            const tr = tbody.insertRow ();
            tr.classList.add ('row');
            for (let j = 0; j < keys.length; j++) {
                const td = tr.insertCell ();
                if (['Aseguradora','Foto'].includes(keys[j])){
                    td.classList.add ('container-img');
                    e[keys[j]].split (',').forEach (e => {
                        const img = document.createElement ('img');
                        img.className = 'img-table';
                        img.src = DRIVE.img + e.toString ();
                        td.appendChild (img);
                    });
                } else {
                    td.appendChild (document.createTextNode (e[keys[j]]));
                }
            }
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


