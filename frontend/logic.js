document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    
    const style = document.createElement('style');
    style.textContent = `
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f0f0f0;
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        .form-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .incidents-list {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .incident-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            position: relative;
        }

        .status {
            padding: 5px 10px;
            border-radius: 15px;
            display: inline-block;
            font-size: 0.8em;
        }

        .status.pendiente { background: #ffd700; }
        .status.en-proceso { background: #90EE90; }
        .status.resuelto { background: #87CEEB; }

        form div {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        button.delete {
            background: #ff4444;
        }

        .timestamp {
            color: #666;
            font-size: 0.8em;
        }
    `;
    document.head.appendChild(style);

    const container = document.createElement('div');
    container.className = 'container';

    const formSection = document.createElement('section');
    formSection.className = 'form-section';
    
    const formTitle = document.createElement('h2');
    formTitle.textContent = 'Reportar Nuevo Incidente';
    
    const form = document.createElement('form');
    form.id = 'incidentForm';
    
    const reporterDiv = document.createElement('div');
    const reporterLabel = document.createElement('label');
    reporterLabel.textContent = 'Reportado por:';
    const reporterInput = document.createElement('input');
    reporterInput.type = 'text';
    reporterInput.id = 'reporter';
    reporterInput.required = true;
    
    const descDiv = document.createElement('div');
    const descLabel = document.createElement('label');
    descLabel.textContent = 'Descripción:';
    const descTextarea = document.createElement('textarea');
    descTextarea.id = 'description';
    descTextarea.rows = 4;
    descTextarea.required = true;
    const descSmall = document.createElement('small');
    descSmall.textContent = 'Mínimo 10 caracteres';
    
    const submitButton = document.createElement('button');
    submitButton.type = 'submit';
    submitButton.textContent = 'Crear Reporte';

    reporterDiv.appendChild(reporterLabel);
    reporterDiv.appendChild(reporterInput);
    descDiv.appendChild(descLabel);
    descDiv.appendChild(descTextarea);
    descDiv.appendChild(descSmall);
    form.appendChild(reporterDiv);
    form.appendChild(descDiv);
    form.appendChild(submitButton);
    
    formSection.appendChild(formTitle);
    formSection.appendChild(form);
    
    const listSection = document.createElement('section');
    listSection.className = 'incidents-list';
    
    const listTitle = document.createElement('h2');
    listTitle.textContent = 'Incidentes Reportados';
    
    const incidentsContainer = document.createElement('div');
    incidentsContainer.id = 'incidentsContainer';
    
    listSection.appendChild(listTitle);
    listSection.appendChild(incidentsContainer);
    
    container.appendChild(formSection);
    container.appendChild(listSection);
    
    const mainTitle = document.createElement('h1');
    mainTitle.textContent = 'Gestor de Incidentes';
    body.appendChild(mainTitle);
    body.appendChild(container);

    const API_URL = '/incidents';

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const incident = {
            reporter: reporterInput.value,
            description: descTextarea.value
        };

        if (incident.description.length < 10) {
            alert('La descripción debe tener al menos 10 caracteres');
            return;
        }

        try {
            await createIncident(incident);
            form.reset();
            fetchIncidents();
        } catch (error) {
            console.error('Error:', error);
        }
    });

    async function fetchIncidents() {
        try {
            const response = await fetch(API_URL);
            if (!response.ok) throw new Error('Error al obtener incidentes');
            
            const incidents = await response.json();
            renderIncidents(incidents);
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function createIncident(incident) {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(incident)
            });
            
            if (!response.ok) throw new Error('Error al crear incidente');
            return await response.json();
        } catch (error) {
            console.error('Error creating incident:', error);
            throw error;
        }
    }
    async function deleteIncident(id) {
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE'
            });
            
            if (!response.ok) throw new Error('Error al eliminar');
            fetchIncidents();
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function updateStatus(id, newStatus) {
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ status: newStatus })
            });
            
            if (!response.ok) throw new Error('Error al actualizar');
            fetchIncidents();
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function renderIncidents(incidents) {
        incidentsContainer.innerHTML = '';

        incidents.forEach(incident => {
            const card = document.createElement('div');
            card.className = 'incident-card';
            card.innerHTML = `
                <h3>${incident.reporter}</h3>
                <p>${incident.description}</p>
                <div class="controls">
                    <select class="status-select" data-id="${incident.id}">
                        <option value="pendiente" ${incident.status === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                        <option value="en proceso" ${incident.status === 'en proceso' ? 'selected' : ''}>En proceso</option>
                        <option value="resuelto" ${incident.status === 'resuelto' ? 'selected' : ''}>Resuelto</option>
                    </select>
                    <button class="delete" data-id="${incident.id}">Eliminar</button>
                </div>
                <div class="timestamp">Creado: ${new Date(incident.created_at).toLocaleString()}</div>
            `;

            const statusSelect = card.querySelector('.status-select');
            statusSelect.classList.add('status', statusToClass(incident.status));
            
            statusSelect.addEventListener('change', (e) => {
                updateStatus(e.target.dataset.id, e.target.value);
            });

            card.querySelector('.delete').addEventListener('click', (e) => {
                if (confirm('¿Estás seguro de eliminar este incidente?')) {
                    deleteIncident(e.target.dataset.id);
                }
            });

            incidentsContainer.appendChild(card);
        });
    }

    function statusToClass(status) {
        return {
            'pendiente': 'pendiente',
            'en proceso': 'en-proceso',
            'resuelto': 'resuelto'
        }[status];
    }

    fetchIncidents();
});