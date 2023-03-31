function fetchParticipants(trajetId) {
    fetch(`/note_participants/${trajetId}`)
        .then(response => response.json())
        .then(data => displayParticipants(data));
}

function displayParticipants(participants) {
    const participantsList = document.getElementById('selectParticipantsList');

    if (participantsList) {
        // Remove existing options
        while (participantsList.firstChild) {
            participantsList.removeChild(participantsList.firstChild);
        }

        participants.forEach(participant => {
            const optionItem = document.createElement('option');
            optionItem.value = participant.id;
            optionItem.textContent = `${participant.prenom} ${participant.nom}`;
            if (participant.selected) {
                optionItem.selected = true;
            }
            participantsList.appendChild(optionItem);
        });

        // Make the participantsList visible
        participantsList.style.display = 'block';
    } else {
        console.error('selectParticipantsList element not found');
    }
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
