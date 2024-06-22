document.addEventListener('DOMContentLoaded', function() {
    const apiUrl = 'http://127.0.0.1:80/disaster/rest/api.php';
    
    function fetchDisasters() {
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('disaster-table-body');
                tableBody.innerHTML = '';

                if (data.records && data.records.length > 0) {
                    data.records.forEach(disaster => {
                        const row = document.createElement('tr');
                        
                        row.innerHTML = `
                            <td>${disaster.id}</td>
                            <td>${disaster.name}</td>
                            <td>${disaster.description}</td>
                            <td>${disaster.date}</td>
                            <td>${disaster.location}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="openEditModal(${disaster.id})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteDisaster(${disaster.id})">Delete</button>
                            </td>
                        `;

                        tableBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    const cell = document.createElement('td');
                    cell.setAttribute('colspan', '6');
                    cell.textContent = 'No data available';
                    row.appendChild(cell);
                    tableBody.appendChild(row);
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    function openAddModal() {
        document.getElementById('disasterForm').reset();
        document.getElementById('id').value = '';
        document.getElementById('disasterModalLabel').textContent = 'Add Disaster';
    }

    function openEditModal(id) {
        fetch(`${apiUrl}?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('id').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('description').value = data.description;
                document.getElementById('date').value = data.date;
                document.getElementById('location').value = data.location;
                document.getElementById('disasterModalLabel').textContent = 'Edit Disaster';
                $('#disasterModal').modal('show');
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    function deleteDisaster(id) {
        fetch(apiUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            fetchDisasters();
        })
        .catch(error => {
            console.error('Error deleting data:', error);
        });
    }

    document.getElementById('disasterForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const id = document.getElementById('id').value;
        const name = document.getElementById('name').value;
        const description = document.getElementById('description').value;
        const date = document.getElementById('date').value;
        const location = document.getElementById('location').value;

        const method = id ? 'PUT' : 'POST';
        const body = {
            id: id,
            name: name,
            description: description,
            date: date,
            location: location
        };

        fetch(apiUrl, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(body)
        })
        .then(response => response.json())
        .then(data => {
            $('#disasterModal').modal('hide');
            fetchDisasters();
        })
        .catch(error => {
            console.error('Error saving data:', error);
        });
    });

    fetchDisasters();
});
