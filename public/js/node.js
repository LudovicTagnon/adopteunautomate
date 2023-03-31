function fetchParticipants(trajetId) {
    fetch(`/note_participants/${trajetId}`)
        .then(response => response.json())
        .then(data => displayParticipants(data));
}

function displayParticipants(participants) {
    const participantsList = document.getElementById('participantsList');
    participantsList.innerHTML = '';

    participants.forEach(participant => {
        const listItem = document.createElement('li');
        listItem.textContent = `${participant.prenom} ${participant.nom}`;
        participantsList.appendChild(listItem);
    });
}

$(document).ready(function() {
    $('#form_trajet').submit(function(event) {
        event.preventDefault();
        var trajetId = $(':input.trajet-id').val();

        // Add a console log to check the value of trajetId
        console.log('trajetId:', trajetId);

        if (trajetId) {
            fetchParticipants(trajetId);
        } else {
            alert('Trajet ID is undefined');
        }
        console.log("JavaScript file loaded");
    });
});

