const filter_content_insurance = document.getElementById ('seguro-filter-content'),
    filter_content_insurer = document.getElementById ('aseguradora-filter-content'),
    clients = document.getElementById ('total-clients'), quotes = document.getElementById ('total-quotes'),
    policies = document.getElementById ('total-policies'), elements = {
        'Vigente': document.getElementById ('active'),
        'No vigente': document.getElementById ('expired'),
        'Proxima a vencer': document.getElementById ('soon-to-beat'),
    };

async function index(){
    const res = await fetch ('usuarios/dashboard', {
        method: 'POST',
        body: new URLSearchParams ({
                                       request: true,
                                   }),
    })
    const data = await res.json ();
    data['insurances'].forEach (e => multiple_option (e['id_seguro'], e['seguro'], filter_content_insurance));
    data['insurers'].forEach (e => multiple_option (e['id_aseguradora'], e['aseguradora'], filter_content_insurer));
    clients.textContent = data['total_clients'];
    quotes.textContent = data['total_quotes'];
    policies.textContent = data['total_policies'];
    table (data['recent_quotes']);
    data['count_states_policies'].forEach (e => {
        elements[e['Estado']].textContent = e['Total'];
    });
}